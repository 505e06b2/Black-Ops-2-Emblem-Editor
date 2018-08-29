import glob
from PIL import Image

bg = Image.new("RGBA", (354,354), (255,255,255, 255))
for x in glob.glob('*.png'):
    im = Image.open(x).crop((315, 151, 669, 505)).convert("L")
    bg.putalpha(im)
    bg.save(x)
