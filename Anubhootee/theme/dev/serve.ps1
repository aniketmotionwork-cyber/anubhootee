# Local static server for theme/ (same job as serve.py, for machines without Python).
# Adds CORS + Private Network Access headers so playground.wordpress.net can fetch the theme zip.
# Optional -Root serves another folder, e.g. -Root ..\..\design on port 4321 for the HTML mockups.
param([int]$Port = 4322, [string]$Root = '')

$root = if ($Root) { (Resolve-Path (Join-Path $PSScriptRoot $Root)).Path } else { Split-Path -Parent $PSScriptRoot }   # default: theme/
$types = @{
  '.html' = 'text/html; charset=utf-8'; '.js' = 'text/javascript'; '.css' = 'text/css'
  '.json' = 'application/json'; '.php' = 'text/plain; charset=utf-8'; '.zip' = 'application/zip'
  '.jpg' = 'image/jpeg'; '.png' = 'image/png'; '.svg' = 'image/svg+xml'; '.md' = 'text/plain; charset=utf-8'
}

$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add("http://localhost:$Port/")
$listener.Start()
Write-Host "Serving $root on http://localhost:$Port/"

while ($listener.IsListening) {
  $ctx = $listener.GetContext()
  $req = $ctx.Request; $res = $ctx.Response
  $res.Headers.Add('Access-Control-Allow-Origin', '*')
  $res.Headers.Add('Access-Control-Allow-Methods', 'GET, OPTIONS')
  $res.Headers.Add('Access-Control-Allow-Headers', '*')
  $res.Headers.Add('Access-Control-Allow-Private-Network', 'true')
  $res.Headers.Add('Cache-Control', 'no-store')
  try {
    if ($req.HttpMethod -eq 'OPTIONS') { $res.StatusCode = 204; continue }
    $rel = [Uri]::UnescapeDataString($req.Url.AbsolutePath.TrimStart('/'))
    if ($rel -eq '') { $rel = 'index.html' }
    $path = [IO.Path]::GetFullPath((Join-Path $root $rel))
    if (-not $path.StartsWith($root, [StringComparison]::OrdinalIgnoreCase) -or -not (Test-Path $path -PathType Leaf)) {
      $res.StatusCode = 404; continue
    }
    $ext = [IO.Path]::GetExtension($path).ToLower()
    $res.ContentType = if ($types.ContainsKey($ext)) { $types[$ext] } else { 'application/octet-stream' }
    $bytes = [IO.File]::ReadAllBytes($path)
    $res.ContentLength64 = $bytes.Length
    $res.OutputStream.Write($bytes, 0, $bytes.Length)
  } catch {
    $res.StatusCode = 500
  } finally {
    $res.Close()
  }
}
