# Build the installable theme zip on Windows: theme/build/anubhootee.zip
# (Same result as build.sh. Uses forward slashes inside the zip so WordPress on Linux unpacks it correctly.)
$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression, System.IO.Compression.FileSystem

$theme = Split-Path -Parent $PSScriptRoot
$src   = Join-Path $theme 'anubhootee'
$out   = Join-Path $theme 'build\anubhootee.zip'
New-Item -ItemType Directory -Force (Split-Path $out) | Out-Null
if (Test-Path $out) { Remove-Item $out -Force }

$zip = [System.IO.Compression.ZipFile]::Open($out, 'Create')
try {
	Get-ChildItem $src -Recurse -File | Where-Object { $_.Name -ne '.DS_Store' -and $_.Name -ne 'Thumbs.db' } | ForEach-Object {
		$rel = 'anubhootee/' + $_.FullName.Substring($src.Length + 1).Replace('\', '/')
		[System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $rel, 'Optimal') | Out-Null
	}
} finally {
	$zip.Dispose()
}
"Built $out ({0:N1} MB)" -f ((Get-Item $out).Length / 1MB)
