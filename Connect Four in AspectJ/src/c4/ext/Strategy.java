package c4.ext;

import java.util.ArrayList;
import java.util.Random;

import c4.model.Board;
import c4.model.Board.Place;

public class Strategy {

	//private Board board;
	
	
	public Strategy(){
		//this.board = board;
	}
	
	/**
	 * Return a random column checking that it's not already 
	 * full on the board
	 * @return
	 */
	public int random(Board board){
		Random rand = new Random();
		int col = -1;
		
		do{
			col = rand.nextInt(7);
		}while(!board.isSlotOpen(col) && !board.isFull());
		
		return col;
		
	}
	
	/**
	 * Return a smart move that blocks three tokens in a row, or wins if immediately possible
	 * @param player
	 * @return
	 */
	public int smart(Board board){
		ArrayList<Place> places = dropablePlaces(board);
		
		for(Place place : places){
			
			// Vertical
			int vertCheck = verticalCheck(place, board);
			if(vertCheck != -1){
				return vertCheck;
			}
			
//			// Horizontal
//			int horizCheck = horizontalCheck(place, board);
//			if(horizCheck != -1) {
//				return horizCheck;
//			}
//			
//			// Diagonal Tl - BR
//			
//			//Diagonal TR - BL
		}
		
		
		return random(board);
	}
	
	/**
	 * Counts tokens vertically and places them into an array. If there are three tokens of the same player in a row, return that col;
	 * @param place
	 * @param board
	 * @return
	 */
	private int verticalCheck(Place place, Board board){
		ArrayList<Place> places = new ArrayList<>();
		
		String pName = "";
		for(int min = place.y+1; min < board.slotHeight() && min < place.y+4; min++){
			Place p = new Place(place.x,min);
			if(pName.equals("")) {
				pName = board.playerAt(place.x, min).name();
				places.add(p);
			} else if (board.playerAt(place.x, min).name().equals(pName)){
				places.add(p);
			} else {
				places.clear();
			}
		}
		
		if(places.size() == 3){
			return place.x;
		}
		
		return -1;
	}
	
	/**
	 * 
	 * @param place
	 * @param board
	 * @return
	 */
	private int horizontalCheck(Place place, Board board) {
		ArrayList<Place> places = new ArrayList<>();
		
		String pName = "";
		
		int xMin = (place.x - 3 >= 0) ? place.x - 3 : 0;
		for(;xMin < board.numOfSlots() && xMin < place.x + 3; xMin++){
			Place p = new Place(xMin, place.y);
			//boolean isEmpty = 
			if(pName.equals("")) {
				System.out.println("xMin: " + xMin);
				System.out.println("place.y: " + place.x);
				System.out.println("Board.playerAt(xMin, place.y): " + board.playerAt(xMin, place.y));
				if(!board.isEmpty(xMin, place.y)){
					pName = board.playerAt(xMin, place.y).name();
					places.add(p);
				}
			} else if (board.playerAt(xMin, place.y).name().equals(pName)){
				places.add(p);
			} else {
				places.clear();
			}
		}
		
		if(places.size() == 3){
			return place.x;
		}
		
		return -1;
	}
	
	/**
	 * Returns list of possible places to drop tokens into
	 * @param board
	 * @return
	 */
	private ArrayList<Place> dropablePlaces(Board board){
		ArrayList<Place> places = new ArrayList<>();
		boolean found = false;
		
		for(int x = 0; x < board.numOfSlots() && board.isSlotOpen(x) && !board.isSlotFull(x); x++){
			for(int y = board.slotHeight() - 1; y >= 0 && !found; y--){
				if(board.isEmpty(x,y)){
					places.add(new Place(x,y));
					found = true;
				}
			}
			
			found = false;
		}
		return places;
	}
	
	public void printPlace(Place place){
		System.out.println("("+place.x+","+place.y+")");
	}
	
	public void printPlaces(Board board){
		ArrayList<Place> places = dropablePlaces(board);
		
		System.out.println("DROPABLE PLACES (x,y)");
		
		for(Place place: places){
			System.out.println("("+place.x+","+place.y+")");
		}
	}
	
}
