import sys

#id,name_short,name_long

class Struct(object):
    def __init__(self, vals):
        self.short = vals[0].upper()
        self.full = vals[1]

    def __str__(self):
        return "INSERT INTO `classes`(`name_short`, `name_long`) VALUES ('%s', '%s');\n" % (self.short, self.full)

def dump(classes):
    with open("classes.sql", "w") as target:
        for cls in classes:
            target.write(str(cls))


def main(fn):
    classes = []

    with open(fn, "r") as source:
        for line in source.readlines():
            vals = line.replace("\n", "").split(";")
            classes.append(Struct(vals))

    dump(classes)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        main(sys.argv[1])