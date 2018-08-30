import os

html = """<html>
<head>
    <title>Black Ops II Playercard Backgrounds</title>
    <link rel="icon" type="image/png" href="../icon.png">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <style>
        div {
            text-align: center;
            padding-top: 30px;
        }

        body {
            overflow: auto;
        }

        img {
            padding: 5px;
        }

        img:hover {
            cursor:pointer;
        }

        div {
            font-size: 3vh;
        }
    </style>
    <script>
        document.onclick = function(e) {
            if(!e.target.src) return;
            var filename = window.location.href.split("/").slice(-1)[0];
            window.prompt("Your link is:\\n(Make sure to copy this, and paste it in the main window)", "backgrounds/" + e.target.src.replace(window.location.href.replace(filename, ""), ""));
        }
    </script>
</head><body><div>Click a background to copy the link to your clipboard</div>"""

for folder in ["custom", "dlc", "general", "game modes", "prestige", "weapons"]:
    html += "<br><div>" + folder + "</div>"
    os.chdir(folder)
    for x, y, f in os.walk("."):
        for x in f:
            print x
            html += '<img src="' + folder + '/' + x + '">'
    os.chdir("..")

html += "</body></html>"
with open("index.html", "w") as f:
    f.write(html)
