import os
import re

# Old button pattern to find
old_pattern = re.compile(
    r'<div class="hidden md:block">\s*<a href="contacto\.html"\s*class="bg-slate-900 hover:bg-primary text-white text-sm font-bold py-2\.5 px-6 rounded transition-all duration-300 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">\s*Solicitar Estudio\s*</a>\s*</div>',
    re.DOTALL
)

# New button content from index.html
new_content = """<div class="hidden md:block">
                        <a href="contacto.html"
                            class="px-6 py-2.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold hover:scale-105 transition-transform">Solicitar
                            Estudio</a>
                    </div>"""

dir_path = r'd:\.gemini\antigravity\scratch\ROMVILL-DEFINITIVO-PERFECCIONADO'
html_files = [f for f in os.listdir(dir_path) if f.endswith('.html')]

for filename in html_files:
    filepath = os.path.join(dir_path, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Replace
    new_text, count = old_pattern.subn(new_content, content)
    if count > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_text)
        print(f'Updated {filename}')
    else:
        print(f'No match in {filename}')
