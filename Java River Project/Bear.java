package proj2;
public class Bear extends Animal{

    //Variables



    //Constructor
    public Bear(){
        super();
    }

    //Getters and Setters

    public int getHealth() {
        return super.getHealth();
    }

    public void setHealth(int health) {
        super.setHealth(health);
    }


    //Other Methods
    public void eat(){
        super.setHealth(5);
    }

    public void hungry(){
        super.setHealth(super.getHealth() - 1);
    }

    @Override
    public String toString() {
        return "Bear";
    }
}