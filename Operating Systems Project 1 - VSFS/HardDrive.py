
class Block:
    MAX_LENGTH = 20
    data = ""

    def __init__(self, string):
        if len(string) <= self.MAX_LENGTH:
            self.data = string
        else:
            self.data = string[:20]
        pass

    def get_size(self):
        return len(self.data)

    def get_data(self):
        return self.data


class Inode:
    # Note: Inodes do not store blocks, only locations. To create an Inode, the loc for each block must be found first.
    blocks = []
    file_name = ""
    author = ""
    size = 0

    # https://stackoverflow.com/questions/12448414/this-constructor-takes-no-arguments-error-in-init
    def __init__(self, name, auth):
        self.file_name = name
        self.author = auth

    def set_blocks(self, b_arr):
        self.blocks = b_arr

    def add_block(self, block_loc, block_size):
        self.blocks.append(block_loc)
        # access the data on the disk w/ the coordinates + get the length
        self.size += block_size

    def get_blocks(self):
        return self.blocks

    def get_file_name(self):
        return self.file_name


class Disk:
    # Globals:
    INODES_PER_PARTITION = None
    DISK_SIZE = None
    PARTITION_SIZE = None

    i_bit_map = None
    d_bit_map = None
    disk = None

    def __init__(self):
        # Set up the blank disk:
        self.disk = [[0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]]

        # Set up the inode section of the disk by declaring them as empty arrays
        for i in range(3, len(self.disk[0])):
            self.disk[0][i] = []

        self.i_bit_map = [[0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0]]       # I bit map 2-D!
        self.d_bit_map = [[0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]]

        superblock = {"Number of Partitions": 10,
                      "Block spaces per Partition": 10,
                      "Inodes per Block Space": 3,
                      "Inode table begins": [0][3]}
        self.disk[0][0] = superblock

    def find_empty_space(self, inode):
        """
        Finds an empty space on the disk for either a new inode or a new block
        :param inode: boolean. True if the data to be stored is an inode
        :return: [2]: the coordinates of the empty disk space
        """
        # if the data to be stored is a block:
        if not inode:
            for i in range(1, len(self.disk) - 1):  # loop through the groups in the disk, skipping the inode section
                for j in range(len(self.disk[i])):  # loop through the given group
                    if self.disk[i][j] == 0:
                        return [i, j]
            print("Didn't find a single match!")
            return False
        # If the data to be stored is an inode:
        else:
            for j in range(3, len(self.disk[0]) - 1):  # Loop through inode section
                if len(self.disk[0][j]) < 3:
                    return [j, len(self.disk[0][j])]
            print("There is no room for a new file.")
            return False

    def save_data(self, data, name, user):
        """
        Takes a file, breaks it up into blocks, stores each block in an empty space, adds the block to an inode, and saves the inode
        :param data: String. The file
        :param name: String. The file name
        :return: none
        """
        d = data
        node = Inode(name, user)
        more_data = True  # True if there is more data to be stored in the disk

        while more_data:
            block = Block(d)
            if len(d) > block.MAX_LENGTH:
                d = d[block.get_size():]
            else:
                more_data = False

            loc = self.find_empty_space(False)  # find an empty space on the disk
            node.add_block(loc, block.get_size())  # add the block location to the file's inode
            self.disk[loc[0]][loc[1]] = block  # put the block in the empty spot on the disk

        inode_spot = self.find_empty_space(True)  # Find an empty spot for the inode
        self.disk[0][inode_spot[0]].append(node)    # Store the node in the empty spot

        self.update_bit_map("data", node.get_blocks())  # update the data bit map by passing the block locations
        self.update_bit_map("inode", inode_spot)  # update the inode bit map

        pass

    def update_bit_map(self, bit_map, locations):
        """
        :param bit_map: data if it is the data bit map, inode if it is the inode bitmap
        :param locations: int[[1][1]]. the locations of the blocks to be added
        :return: [][] bitmap
        """
        # There are only 9 sections in data bit map, so have to subtract 1 to make the bit map and the locations match
        if bit_map == "data":

            for loc in locations:
                if self.d_bit_map[loc[0] - 1][loc[1]] == 0:
                    self.d_bit_map[loc[0] - 1][loc[1]] = 1

        elif bit_map == "inode":
            if self.i_bit_map[locations[0] - 3][locations[1]] == 0:
                self.i_bit_map[locations[0] - 3][locations[1]] = 1

        return bit_map

    def get_files(self):
        """
        Returns file names and locations for a full library listing. Does NOT return file data
        :return: a list of all file names
        """
        file_names = []

        for i in range(len(self.i_bit_map) - 1):
            # Acknowledged Discrepancy in the way the inode bit map is handled and the way the disk is handled.
            for j in range(len(self.disk[0][i + 3])):
                file_names.append(self.disk[0][i + 3][j].get_file_name())
        return file_names

    def find_file(self, search_key):
        """
        Returns a list of dictionaries containing the name and inode location for any file
        with the search string in its name
        :param search_key: the string used in the search
        :return: the list of dictionaries
        """
        match_list = []

        for i in range(len(self.i_bit_map)):
            for j in range(len(self.i_bit_map[i])):
                if self.i_bit_map[i][j] == 1:
                    name = self.disk[0][i + 3][j].get_file_name()
                    if name.find(search_key) != -1:
                        match = {"Name": name,
                                 "inode i": i,
                                 "inode j": j}
                        match_list.append(match)
        return match_list

    def open_file(self, inode_location):
        """
        returns the string associated with the file by combining all block string elements
        :param inode_location: [j, k] the inode location
        :return: String. the full string associated with the file
        """
        full_string = ""
        inode = self.disk[0][inode_location[0] + 3][inode_location[1]]
        block_locs = inode.get_blocks()
        for block in block_locs:
            full_string += self.disk[block[0]][block[1]].get_data()
        return full_string

    def delete_file(self, loc):
        """
        Deletes a file by removing all blocks associated with the appropriate inode, then the inode
        :param loc: [j, k] location of the inode
        :return: none
        """
        blocks = self.disk[0][loc[0] + 3][loc[1]].get_blocks()

        for i in range(len(blocks)):
            self.disk[blocks[i][0]][blocks[i][1]] = 0

        self.disk[0][loc[0] + 3].pop(loc[1])

    def edit_file(self, loc, nstr):
        """
        "Edits" a file by saving the name of the previous file, deleting the old file, and creating
        a new file with the new data under the same name
        :param loc: the location of the inode for the old file
        :param nstr: the new data
        :return: none
        """
        file_name = self.disk[0][loc[0] + 3][loc[1]].get_file_name()
        self.delete_file(loc)
        self.save_data(nstr, file_name)

