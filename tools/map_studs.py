import sys

# id,uname,password,role,name,surname,in_class

CLASSES = {
    "E1FI" : 1,
    "E2FI" : 2,
    "E3FI" : 3,
    "FKM" : 6,
    "FIB" : 7,
    "MEL1" : 8,
    "MEL2" : 9,
    "MEL3" : 10,
    "BKM" : 11
}

class Struct(object):
    def __init__(self, vals):
        self.name = vals[1]
        self.surname = vals[0] 
        self.role = vals[2]
        try:
            self.in_class = CLASSES[vals[3].upper()]
        except:
            self.in_class = "NULL"
            print(vals)
        self.uname = self.name.lower() + "." + self.surname.lower()
        self.password = "init"

    def __str__(self):
        return "INSERT INTO `users`(`uname`,`password`,`role`,`name`,`surname`,`in_class`) VALUES ('%s',PASSWORD('%s'),'%s','%s','%s',%s);\n" % (self.uname,self.password,self.role,self.name,self.surname,self.in_class);
        #return ";".join([self.uname, "PASSWORD("+self.password+")", self.role, self.name, self.surname, self.in_class]);

def dump(users):
    with open("users.sql", "w") as target:
        for user in users:
            target.write(str(user))


def main(fn):
    used_names = set()
    users = []

    with open(fn, "r") as source:
        for line in source.readlines():
            vals = line.replace("\n", "").split(";")

            obj = Struct(vals)

            while True:
                if obj.uname not in used_names:
                    used_names.add(obj.uname)
                    break
                else:
                    obj.uname += "1"

            users.append(obj)

    dump(users)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        main(sys.argv[1])