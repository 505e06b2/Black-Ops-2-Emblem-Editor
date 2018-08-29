import os, string

folder = "screenshots"

filenames = []
#filenames += ["Letter " + x for x in list(string.ascii_uppercase)]
#filenames += ["Zero", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"]
"""
filenames += ["Half Circle", "Quarter Circle", "Half Heart", "Cone", "Thimble", "Kiss", "Scribble", "Round Square", "Ninja Star", "Half Star", "Shuriken", "Half Shuriken", "Lamp Shade",
              "Pyramid", "Half Tube", "Tube", "Golf Flag", "Tongue", "Broken Column", "Visor", "Bone", "Armchair", "Oven Mitt", "Wind Sock", "Podium", "Pie Slice", "Flashlight", "Scoop",
              "Flag Breeze", "Flag No Wind", "Axe", "Fedora", "Rock", "Bike Ramp", "Rock Shadow", "Half Column", "Monolith", "Top Hat", "Igloo", "Mane", "Swoop", "Shield", "Paint Splash",
              "Pillow", "Asterisk Full", "Biohazard", "Curved Line", "Smile Outline", "Heart", "Ice Star", "Triangle Wide", "Tent", "Half Short Hair", "Half Mustache", "Half Long Hair",
              "Full Circle", "Circle 02", "Diamond", "Rectangle Medium", "Square Full", "Treyarch"]
"""
#filenames += ["Private 1st Class", "Lance Corporal", "Corporal", "Sergeant", "Staff Sergeant", "Gunnery Sergeant", "Master Sergeant", "Master Gunnery Sergeant", "Second Lieutenant", "Lieutenant", "Captain", "Major", "Lt. Colonel", "Colonel", "Brigadier General", "Major General", "Lt. General", "General", "Commander"]
#filenames += [x + " Qualified" for x in ["KAP-40", "Tac-45", "B23R", "Executioner", "Five-seven", "MP7", "Skorpion EVO", "PDW-57", "Chicom CQB", "MSMC", "Vector K10", "M8A1", "SCAR-H", "AN-94", "SWAT-556", "Type 25", "FAL OSW", "SMR", "M27", "MTAR", "Mk 48", "QBB LSW", "LSAT", "HAMR", "Ballista", "SVU-AS", "DSR 50", "XPR-50", "R870 MCS", "M1216", "S12", "KSG", "SMAW", "FHJ-18 AA", "RPG", "Assault Shield", "Crossbow", "Ballistic Knife", "Peacekeeper"]]

#filenames += ["Elite Member", "Elite Founder", "Default Emblem", "Crushing Victory", "Crushing Victory ", "Crushing Victory  ", "Crushing Victory   ",  "Crushing Victory    ", "Shutout", "Shutout ", "Crushing Victory     ", "Annihilation Victory", "Relentless", "Triple Kill", "Avenger", "Savior", "Unstoppable", "Ninja", "Last Man Standing", "The Finisher", "Shutout Round", "Bomb Protector", "Interruption", "Bomb Protector ", "Interruption ", "Super Star", "Double Denied", "Bravo Hot", "Alpha Lockdown", "Bravo Lockdown", "Charlie Lockdown", "Synchronized Attack", "Point Man", "Zone Sweep", "Trick Shot", "Clean House", "Slice 'n Dice", "Wet Work", "Situation Critical", "Far Sighted", "Tick Tick Boom", "Pistoleer", "Say Hello", "Headhunter", "Sharpshooter", "Close Quarters Expert", "Counter Trapper", "Surprise Package", "Counter Hacker", "Aircraft Hunter", "Clean Sweep", "Grab n Go", "Protected Kill", "Close Call", "Arch Nemesis", "Circus Act", "Found Kills", "Short Fuse", "High Voltage", "Follow Through", "Stick Around", "Hail Mary", "Brutal Killer", "Fury Killer", "Frenzy Killer", "Super Killer", "All Clear", "Assisted Homicide", "Backdraft", "Guerilla Warfare", "Vandalism", "Action Hero", "Commando", "Perk Greed", "Danger Close", "Overkill", "Gunfighter", "Killjoy", "Pest Control", "Drones Eliminated", "Dog Pound", "Down Dog", "Opportunistic", "Maximum Payload", "Anti-Swatter", "Threat Neutralized", "Special Delivery", "RC Multi Bomber", "Heavy Cover", "Thumper", "Shredder", "Focus Fire", "Tracker", "Guide Dogs", "Hard Counter", "Overcooked", "Cancelled Out", "Make It Rain", "Got Your Back", "Small Game Hunter", "Big Game Hunter", "Thief", "Merciless", "Ruthless", "Hard to Kill", "Invincible"]

os.chdir(folder)
index = 0
for x, y, f in os.walk("."):
    for x in f:
        print filenames[index]
        os.rename(x, filenames[index] + ".png")
        index += 1
