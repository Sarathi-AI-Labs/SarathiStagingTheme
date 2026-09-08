# PowerShell script to fix PHP style issues in the custom theme
$themePath = "C:\Users\Bhakti\Local Sites\unitedschool\app\public\wp-content\themes\custom-theme"

Get-ChildItem -Path $themePath -Recurse -Filter *.php | ForEach-Object {
    $file = $_.FullName
    # Read file content
    $content = Get-Content -Raw -Path $file
    # 1. Convert leading spaces (4 spaces) to tabs
    $content = $content -replace "(?m)^( {4})+", {
        param($m) $m.Value -replace " {4}", "`t"
    }
    # 2. Ensure exactly one blank line after a file‑level doc comment (/** ... */)
    $content = $content -replace "(?s)(/\*\*.*?\*/)(\r?\n)(?!\r?\n)", "`$1`r`n`r`n"
    # 3. Replace short ternary expressions with full ternary
    # a) Assignment form: $var = $a ?: $b;
    $content = $content -replace "(?m)(\$[A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.+?)\s*\?\:\s*(.+?);", "`$1 = `$2 ? `$2 : `$3;"
    # b) General expression: $a ?: $b
    $content = $content -replace "(?m)(\S+)\s*\?\:\s*(\S+)", "`$1 ? `$1 : `$2"
    # Write back the corrected content
    Set-Content -Path $file -Value $content -Encoding UTF8 -NoNewline
}
