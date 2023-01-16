//https://www.studytonight.com/java/package-in-java.php
package proj2;
//include Animal;

public class River {

    public static void main(String[] args) {
        //Create the river.
        Animal[] river = new Animal[50];
        //Fill the river
        fillRiver(river);


        printRiver(river);
        //Loop through the river 20x to make the 20 rounds of the simulation
        for(int i = 0; i < 20; i++){
            System.out.println();
            System.out.println("Beginning Run " + (int)(i + 1) + " of River");
            System.out.println();

            runRiver(river);

            System.out.println();

            printRiver(river);

            System.out.println();
        }



    }

    public static void printRiver(Animal[] r){
        for(int i = 0; i < r.length; i++){
            if(r[i] == null) System.out.print("    |");
            else {
                System.out.print(r[i].toString() + "| ");
                //Reset all the animals' isMoved properties to false
                r[i].setMoved(false);
            }
        }
        System.out.println();
    }


    public static void runRiver(Animal[] r){

        //loop through the river so that each slot is dealt with
        for(int i = 0; i < r.length; i++){

            //check each location in the river for an animal that has not already been moved this round
            if(!(r[i] == null) && !(r[i].isMoved())){
                                                                //https://www.geeksforgeeks.org/switch-statement-in-java/
                                                                //https://www.w3schools.com/java/java_switch.asp
                int move = calculateAction();
                switch(move){
                    //a positive 1 indicates rightward movement, while a -1 indicates leftward movement
                    case 1:
                        if(i == r.length -1) break;
                        else{
                            moveAnimal(r, 1, i);
                            break;
                        }
                    case -1:
                        if(i == 0) break;
                        else{
                            moveAnimal(r, -1, i);
                            break;
                        }
                    case 0:
                        break;
                }

            }
            //Remove any bears that have starved
            if( (r[i] instanceof Bear) && (r[i].getHealth() <= 0)) r[i] = null;

        }

    }

    public static void moveAnimal(Animal[] r, int d, int i){        //d: direction, i: index
        //Note: this function is only called if river[i] is not null.

        //Make sure a valid input has been given
        if(Math.abs(d) > 1) System.out.println("A move of greater than one place has been attempted.");
        else{
           //if the space the animal is trying to move to is empty, move them there
           if(r[i + d] == null){
               r[i + d] = r[i];
               r[i] = null;
               r[i + d].setMoved(true);

           //https://www.webucator.com/how-to/how-check-object-type-java.cfm#:~:text=You%20can%20check%20object%20type,than%20one%20type%20of%20object.
           }else if(r[i + d] instanceof Bear){

               //if fish moves to bear, bear overrides fish and resets it's health
                if(r[i] instanceof Fish){
                    System.out.println("Fish at " + i + " swims into Bear at " + (int)(i+d));
                    r[i] = null;
                    r[i + d].eat();
                }else { //if both are bears make a baby
                    System.out.print("Bear " + i + " and " + (int)(i+d) + " make a baby at ");
                    makeBaby(r, 'b');
                    r[i].hungry();
                    r[i + d].hungry();
                    r[i].setMoved(true);
                }
            }else if(r[i + d] instanceof Fish){

                if(r[i] instanceof Bear){
                //if bear moves to fish, bear overrides fish and resets it's health
                    System.out.println("r[i] is bear. destroying fish and moving bear.");
                    r[i].eat();
                    r[i + d] = r[i];
                    r[i] = null;
                    r[i + d].setMoved(true);
                }else{ //if both are fish make a baby
                    System.out.print("Fish " + i + " and " + (int)(i+d) + " make a baby at ");
                    makeBaby(r, 'f');
                    r[i].setMoved(true);
                }
            }

        }

    }


    public static void makeBaby(Animal[] r, char type){
        Animal baby;

        //Check to see which type of baby was ordered. If the wrong input was put in, report to the console and break.
        if(type == 'b') baby = new Bear();
        else if(type == 'f') baby = new Fish();
        else{
            System.out.println("A baby of the wrong type was attempted");
            return;
        }

        //Put the baby in a null spot
        for(int i = 0; i < r.length; i++){
            if(r[i] == null){
                r[i] = baby;
                System.out.println(i);
                break;
            }
        }

    }




    public static int calculateAction(){
        //Create a random number generator to decide what each animal will do. We'll make it between 1 and 100 so we can adjust the ratios if we need to
        int rand = (int) (Math.random() * 100) + 1;

        if(rand < 34) return 0; //stay
        else if(rand < 67) return -1; //move left
        else return 1; //move right

    }

    public static void fillRiver(Animal[] r){
        for(int i = 0; i < 50; i++){
            int animalType = (int)Math.floor(Math.random()*30 + 1);
            if(animalType <= 2)    r[i] = new Bear();
            else if(animalType <= 10)    r[i] = new Fish();
            else    r[i] = null;

        }
    }

}
