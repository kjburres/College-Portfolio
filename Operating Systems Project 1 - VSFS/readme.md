# VSFS Project : Kali Cheeley

## Summary: 
This representation of a **very simple file system** is **object oriented**, 
relying on a specific instantiation of a Disk object, 
which is comprised of Inode and Block objects. 
Block objects contain the main data to be stored in the file in fixed amounts, 
and Inode objects contain a list of associated block locations,
as well as a name and author name. The hard drive (Disk) consists of a 10 x 10 multi-dimensional
array. The inode section is from [0][3] - [0][9], with each section
capable of containing 3 inodes as it's own list. 

There are many features I would have liked to include in this file system, 
including many user input boundaries and usability features on the simulator side.
The program still has some bugs when it comes to deleting a file (in the middle of other files)
and adding a new file of larger Block size.

------

## The Core Features of a VSFS include:

### Data Structures:
1. A Hard Drive 
   - Partition the hard drive into block sections 
   - Distinguish between the data region and the rest of the disk 
   - Create an inode table 
   - Pointers or extents
2. Blocks
   - Limit the amount of data that can be stored in each space
3. Inodes 
   - keep track of multiple data locations 
   - hold a file name 
   - Store some sort of metadata about the file  
   - Create a bitmap for the data and for the inodes 
   - Establish a method of updating the bitmaps 
4. A superblock
   - information about the particular file system 
   - how many inodes and data blocks are in the file system 
   - where the inode table begins
5. A Folder Structure 
   - Pointers or extents 
   - Indirect pointers 
   - Root directory

### Other Functions: 
6. Establish a method of finding empty space on the hard drive, both for inodes and data blocks
7. Free space management / pre-allocation methods
8. Dynamic partitioning/caching

### Access Methods:
9. Create
10. Search
11. Write
12. Delete

------
## Notes: 
The superblock is represented partially by the constructor of the Disk class and partially by a directory stored in the [0][0]
block space of the Disk. The only features listed here I did not implement are:
- A Folder Structure, including all it's components
- Free Space Management and Pre-Allocation Methods
- Dynamic Partitioning and Caching