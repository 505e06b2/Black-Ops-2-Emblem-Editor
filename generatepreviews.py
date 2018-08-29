fcontents = ""

for x in range(1,33):
    fcontents += '<div id="layer-' + str(x-1) + '" class="layer-preview" onclick="editor.selectpreview(' + str(x-1) + ')"><img id="layer-img-' + str(x-1) + '" style="filter: url(#filter-' + str(x-1) + ')" src="empty.png"><div class="layername">Layer ' + str(x) + '</div></div>'

with open("previews.html", "w") as f:
    f.write(fcontents)


"""
for x in range(32):
    fcontents += '<filter id="filter-' + str(x) + '"><feColorMatrix id="matrix-' + str(x) + '" in="SourceGraphic" type="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 1 0" /></filter>'

with open("filters.html", "w") as f:
    f.write(fcontents)
"""
