from bs4 import BeautifulSoup
import requests

with open("Playercard_Black Ops II Calling Cards _ Call of Duty Wiki _ FANDOM powered by Wikia.html") as fp:
    soup = BeautifulSoup(fp, "html.parser")

for x in soup.findAll("img", {"class": "thumbimage"}):
    url = x["data-src"].rpartition(".")[0] + ".png"
    r = requests.get(url, stream=True)
    with open(url.rpartition("/")[2], 'wb') as fd:
        for chunk in r.iter_content(chunk_size=512):
            fd.write(chunk)
