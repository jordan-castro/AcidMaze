from PIL import Image
from glob import glob
from random import choice


# Choose a random maze to draw
files = glob('../mazes/*.txt')
file = choice(files)

maze = []

with open(file, "r") as f:
    for line in f:
        line = line.strip()
        if len(line) == 0:
            break

        nigga = []
        for char in line:
            nigga.append(int(char))
        maze.append(nigga)

# RGB for violet
violet = (232, 100, 40, 255)
orange = (100, 44, 145, 255)
vibrant = (98, 72, 189, 255)
white = (255, 255, 255, 255)
black = (0, 0, 0, 255)

image = Image.new("RGBA")
image_name = "../trip_out.png"

# Get size of image from maze
height = len(maze)
width = len(maze[0])

colors = [violet, orange, vibrant]

for y in range(height):
    for x in range(width):
        if maze[y][x] == 1:
            image.putpixel((x, y), black)
        else:
            image.putpixel((x, y), white)

        image.save(image_name)

rows_v = []
cols_v = []

