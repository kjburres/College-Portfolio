import HardDrive

# The Setup:
hd = HardDrive.Disk()
username = input("username: ")
print("Hello, " + username)
run = True


def do_file_action(file, act):
    location = [file["inode i"], file["inode j"]]
    match act:
        case 1:
            print(hd.open_file(location))
            pass
        case 2:
            old_data = hd.open_file(location)
            print("Here is the current phrase:")
            print(old_data)
            new_data = input("Enter your new string: ")
            hd.edit_file(location, new_data)
            pass
        case 3:
            hd.delete_file(location)
            pass
        case 4:
            pass


def file_search():
    print("")
    key = input("Enter a part of your file name: ")
    matches = hd.find_file(key)
    if len(matches) > 0 :
        print("")
        print("Here are the matches:")
        for i in range(len(matches)):
            match = matches[i]
            print(f"[{i}] : {match['Name']}")
        file = int(input("Which file would you like to select?: "))
        print("")
        print("What would you like to do with the file?")
        print(f"[1] : open the file")
        print(f"[2] : edit the file")
        print(f"[3] : delete the file")
        next_action = int(input("Type your answer here: "))
        print(f"matches[file]: {matches[file]}")
        do_file_action(matches[file], next_action)
    else:
        print("Sorry, there were no matches for that search. (Results are case sensitive.)")

def new_file():
    file_name = input("Type the name of your file: ")
    file_data = input("What would you like your file to say?: ")
    print(hd.disk)
    hd.save_data(file_data, file_name, username)
    print("Your file is saved :).")


def list_files():
    print("")
    print("These are your files:")
    files = hd.get_files()
    for i in range(len(files)):
        print(f"[{i}] : {files[i]}")
    pass


def do_action(act):
    match act:
        case 1:
            new_file()
            pass
        case 2:
            list_files()
            pass
        case 3:
            file_search()
            pass
        case 4:
            run = False
            pass


# The Simulator:

while run:
    print("")
    print("What would you like to do?")
    # Wanted to put parameters around this input so that if the entry is not a number the user is re-prompted
    print("[1] : Create a new file")
    print("[2] : List all the files")
    print("[3] : Search for a file")
    print("[4] : Exit the program")
    print("-------------------------------")
    action = input("Type your answer here: ")
    print("")
    do_action(int(action))


