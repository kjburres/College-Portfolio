package proj2;
public class Animal {

    //Variables
    private boolean moved;
    private int health;


    //Constructor
    public Animal(){
        moved = false;
        health = 5;
    }


    //Getters and Setters
    public boolean isMoved() {
        return moved;
    }

    public void setMoved(boolean moved) {
        this.moved = moved;
    }

    public int getHealth() {
        return health;
    }

    public void setHealth(int health) {
        this.health = health;
    }

    //Other Methods
    public void eat(){}

    public void hungry(){}

    @Override
    public String toString() {
        return "Animal{}";
    }
}



