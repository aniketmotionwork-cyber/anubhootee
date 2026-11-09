/**
 * Depth-effect cutouts: finds the main subject of each photo with AI segmentation
 * (BiRefNet Lite, MIT licence, run locally with transformers.js) and writes the subject
 * as a transparent WebP of exactly the same size as the photo.
 *
 * Usage:  node scripts/depth-cutouts.mjs <photo> <cutout.webp> [<photo> <cutout.webp> …]
 * Prints one JSON line per photo: { in, out, coverage } (coverage = share of the photo that is subject).
 * Normally run through:  php artisan anubhootee:depth
 */
import { AutoModel, AutoProcessor, RawImage, env } from '@huggingface/transformers';
import sharp from 'sharp';
import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';

const MODEL = 'onnx-community/BiRefNet_lite-ONNX';
env.allowLocalModels = false; // download from Hugging Face once, then reuse the local cache

const args = process.argv.slice(2);
if (args.length < 2 || args.length % 2) {
	console.error('Usage: node scripts/depth-cutouts.mjs <photo> <cutout.webp> [...]');
	process.exit(1);
}

const model = await AutoModel.from_pretrained(MODEL, { dtype: 'fp32' });
const processor = await AutoProcessor.from_pretrained(MODEL);

for (let i = 0; i < args.length; i += 2) {
	const [input, output] = [args[i], args[i + 1]];
	try {
		const image = await RawImage.read(input);
		const { pixel_values } = await processor(image);
		const { output_image } = await model({ input_image: pixel_values });

		// Soft mask (keeps natural edges like fur and hair), scaled back to the photo's size.
		const mask = await RawImage.fromTensor(output_image[0].sigmoid().mul(255).to('uint8'))
			.resize(image.width, image.height);
		const alpha = Buffer.from(mask.data);

		// Remove faint haze and firm up the interior, without hard-cutting the edges.
		let solid = 0;
		for (let p = 0; p < alpha.length; p++) {
			const a = alpha[p] / 255;
			const v = a < 0.06 ? 0 : Math.min(1, (a - 0.06) / 0.82);
			alpha[p] = Math.round(v * 255);
			if (alpha[p] > 128) solid++;
		}

		// Interleave the photo's RGB with the mask into one RGBA image.
		const { data: rgb, info } = await sharp(input).rotate().removeAlpha().raw().toBuffer({ resolveWithObject: true });
		if (info.width !== image.width || info.height !== image.height) {
			throw new Error(`size mismatch ${info.width}x${info.height} vs ${image.width}x${image.height}`);
		}
		const rgba = Buffer.alloc(info.width * info.height * 4);
		for (let p = 0, q = 0, r = 0; p < alpha.length; p++, q += 4, r += 3) {
			rgba[q] = rgb[r]; rgba[q + 1] = rgb[r + 1]; rgba[q + 2] = rgb[r + 2]; rgba[q + 3] = alpha[p];
		}

		mkdirSync(dirname(output), { recursive: true });
		await sharp(rgba, { raw: { width: info.width, height: info.height, channels: 4 } })
			.webp({ quality: 86, alphaQuality: 92, effort: 5 })
			.toFile(output);

		console.log(JSON.stringify({ in: input, out: output, coverage: +(solid / alpha.length).toFixed(4) }));
	} catch (err) {
		console.log(JSON.stringify({ in: input, out: output, error: String(err && err.message || err) }));
	}
}
