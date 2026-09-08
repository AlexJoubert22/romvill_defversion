import urllib.request
import os

urls = [
    ('heartwarming.mp3', 'https://incompetech.com/music/royalty-free/mp3-royaltyfree/Heartwarming.mp3'),
    ('sovereign.mp3', 'https://incompetech.com/music/royalty-free/mp3-royaltyfree/Sovereign.mp3'),
    ('clear_air.mp3', 'https://incompetech.com/music/royalty-free/mp3-royaltyfree/Clear%20Air.mp3'),
    ('carefree.mp3', 'https://incompetech.com/music/royalty-free/mp3-royaltyfree/Carefree.mp3')
]

os.makedirs('voice_tests', exist_ok=True)
headers = {'User-Agent': 'Mozilla/5.0'}
for fname, url in urls:
    try:
        req = urllib.request.Request(url, headers=headers)
        with urllib.request.urlopen(req, timeout=15) as resp:
            data = resp.read()
            out_p = os.path.join('voice_tests', fname)
            with open(out_p, 'wb') as f:
                f.write(data)
            print(f"Downloaded {fname}: {len(data)} bytes")
    except Exception as e:
        print(f"Failed {fname}: {e}")
