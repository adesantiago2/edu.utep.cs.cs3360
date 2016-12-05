/**
 * @author Tomas Chagoya, Martin Morales
 */

package c4.aspect;

import java.util.List;
import java.awt.Graphics;
import java.awt.event.ActionEvent;
import java.awt.event.KeyEvent;
import java.util.ArrayList;

import javax.swing.AbstractAction;
import javax.swing.ActionMap;
import javax.swing.InputMap;
import javax.swing.JComponent;
import javax.swing.KeyStroke;

import c4.base.BoardPanel;
import c4.base.ColorPlayer;
import c4.base.BoardPanel.Game;
import c4.model.Board;
import c4.model.Player;
import c4.model.Board.Place;
import c4.base.C4Dialog;

public privileged aspect AddCheatKey {

	private static boolean cheatEnabled = false;

	private Board board;
	private BoardPanel boardPanel;
	private PlayerPlace[][] places;
	private List<PlayerPlace> winningPlaces = new ArrayList<PlayerPlace>();		

	/**
	 * Advice that removes highlighted tokens from cheat after a game is over
	 * or a new one is starting
	 */
	pointcut newGameStarting() : call(void C4Dialog.startNewGame()) || call(void C4Dialog.markWin()) ;
	before(): newGameStarting(){
		if(cheatEnabled){
			winningPlaces = new ArrayList<PlayerPlace>();
			cheatEnabled = false;
		}
	}
	
	/**
	 * Advice to capture the board from game
	 * @param b
	 */
	pointcut boardConst(Board b) : initialization(Board.new()) && target(b);
	after(Board b) : boardConst(b) {
		board = b;
		places = new PlayerPlace[b.numOfSlots()][b.slotHeight()];
	}

	/**
	 * advice to implement cheat key
	 * @param bp
	 */
	pointcut boardPanelConst(BoardPanel bp) : initialization(BoardPanel.new(Board, Game)) && target(bp);
	after(BoardPanel bp) : boardPanelConst(bp) {
		ActionMap actionMap = bp.getActionMap();
		int condition = JComponent.WHEN_IN_FOCUSED_WINDOW;
		InputMap inputMap = bp.getInputMap(condition);
		String cheat = "Cheat";
		inputMap.put(KeyStroke.getKeyStroke(KeyEvent.VK_F5, 0), cheat);
		actionMap.put(cheat, new KeyAction(bp, cheat));
		boardPanel = bp;
	}

	/** Pointcut to change the color of the tokens any time a token is dropped or the cheat key is pressed */
	after(): call(int Board.dropInSlot(int, Player)) || execution(void KeyAction.actionPerformed(ActionEvent)) {
		getPlaces();
		scanBoard();
		boardPanel.repaint();
	}

	/**
	 * Creates a board of PlayerPlace objects
	 */
	private void getPlaces() {
		if(board == null) return;

		for(int y = board.slotHeight()-1; y >= 0; y--){
			for(int x = 0; x < board.numOfSlots(); x++){
				Player player = board.playerAt(x, y);
				String p = "";

				if(player == null)
					p = "NULL";
				else
					p = player.name();

				places[x][y] = new PlayerPlace(x,y,p);
			}
		}
	}

	/** Scans board for tokens in rows/cols/diag that have three tokens in a row of the same player
	 *  and an empty place that can be inserted into
	 */
	private void scanBoard() {
		winningPlaces = new ArrayList<>();

		for(PlayerPlace[] placeRow : places)
			for(PlayerPlace placeCol : placeRow) {
				scanHorizontal(placeCol);
				scanVertical(placeCol);
				scanDiagonalOne(placeCol);
				scanDiagonalTwo(placeCol);
			}
	}

	/**
	 * Scans board for three tokens in a row horizontally followed by an empty space 
	 * @param place
	 * @return
	 */
	private boolean scanHorizontal(PlayerPlace place){
		int inARow = 1;

		if(place.player().equals("NULL")) return false;
		int x = place.x;
		int y = place.y;

		//checks for three tokens in a row, from left to right
		for(int offset = 1; offset < 4; offset++) {
			if(places(x + offset, y) != null && place.player().equals(places(x + offset, y).player())) {
				inARow++;
			}
		}

		if(inARow == 3 && checkHorizontalNeighbours(place,3)) {
			for(int offset = 0; offset < 3; offset++) {		// Add winning row to winningPlaces Arraylist

				if(!winningPlaces.contains(places(x + offset, y)))	// only if they aren't already in winningPlaces
					winningPlaces.add(places(x + offset, y));
			}

			return true;
		}

		return false;
	}
	

	/**
	 * Scans for three tokens in a row diagonally from bottom right to top left
	 * followed by a blank space.
	 * @param place
	 * @return
	 */
	private boolean scanDiagonalTwo(PlayerPlace place){
		if(place.player().equals("NULL")) return false;

		int inARow = 1;
		int x = place.x();
		int y = place.y();

		//checks for three tokens in a row, from bottom left to right top
		for(int offset = 1; offset < 4; offset++) {
			if(inBounds(x-offset,y-offset) && 
					places(x-offset, y-offset) != null && 
					place.player().equals(places(x-offset, y-offset).player())
					) 
			{
				inARow++;
			}
		}

		if(inARow == 3 && ((inBounds(x-3,y-3) && isEmpty(x-3,y-3)) || (inBounds(x+1,y+1) && isEmpty(x+1,y+1))))
		{
			for(int offset = 0; offset < 3; offset++) {		// Add winning row to winningPlaces Arraylist
				if(!winningPlaces.contains(places(x-offset, y-offset)))	// only if they aren't already in winningPlaces
					winningPlaces.add(places(x-offset, y-offset));
			}

			return true;
		}
		return false;
	}

	/**
	 * Scans for three tokens in a row diagonally from bottom left to top right
	 * followed by a blank space.
	 * @param place
	 * @return
	 */
	private boolean scanDiagonalOne(PlayerPlace place){
		if(place.player().equals("NULL")) return false;

		int inARow = 1;
		int x = place.x();
		int y = place.y();

		//checks for three tokens in a row, from bottom left to right top
		for(int offset = 1; offset < 4; offset++) {
			if(inBounds(x+offset,y-offset) && 
					places(x+offset, y-offset) != null && 
					place.player().equals(places(x+offset, y-offset).player())
					) 
			{
				inARow++;
			}
		}

		if(inARow == 3 && ((inBounds(x+3,y-3) && isEmpty(x+3,y-3)) || (inBounds(x-1,y+1) && isEmpty(x-1,y+1))))
		{
			for(int offset = 0; offset < 3; offset++) {		// Add winning row to winningPlaces Arraylist
				if(!winningPlaces.contains(places(x+offset, y-offset)))	// only if they aren't already in winningPlaces
					winningPlaces.add(places(x+offset, y-offset));
			}

			return true;
		}
		return false;
	}

	/**
	 * Scans for three tokens in a row vertically
	 * followed by a blank space.
	 * @param place
	 * @return
	 */
	private boolean scanVertical(PlayerPlace place){
		int inARow = 1;

		if(place.player().equals("NULL")) return false;
		int x = place.x;
		int y = place.y;

		//checks for three tokens in a row, from bottom to top
		for(int offset = 1; offset < 4; offset++) {
			if(places(x, y-offset) != null && place.player().equals(places(x, y-offset).player())) {
				inARow++;
			}
		}

		if(inBounds(x,y-3) && inARow == 3 && isEmpty(x,y-3)){
			for(int offset = 0; offset < 3; offset++) {		// Add winning row to winningPlaces Arraylist

				if(!winningPlaces.contains(places(x, y-offset)))	// only if they aren't already in winningPlaces
					winningPlaces.add(places(x, y-offset));
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks if there there is a valid empty spot
	 * helper method to scanHorizontal
	 * @param place
	 * @param offset
	 * @return
	 */
	private boolean checkHorizontalNeighbours(PlayerPlace place, int offset){
		int y = place.y();
		int rightLimit = place.x() + 3;
		int leftLimit = place.x() - 1;

		if(places(rightLimit-1, y) != null){  //empty spots don't count

			if(inBounds(leftLimit,y) && isEmpty(leftLimit,y)){ //check for empty spot on the left
				if(y < 5){ 
					if(isEmpty(leftLimit,(y+1))){
						return false;
					}
				}
				return true;
			}

			if(inBounds(rightLimit,y) && isEmpty(rightLimit,y)){ //check for empty spot on the right
				if(y < 5)
					if(isEmpty(rightLimit,(y+1)))
						return false;
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if coordinate is within bounds of board
	 * @param x
	 * @param y
	 * @return
	 */
	private boolean inBounds(int x, int y){
		return x >= 0 && x < board.numOfSlots() && y >= 0 && y < board.slotHeight();
	}

	/**
	 * Checks if coordinate is empty
	 * @param x
	 * @param y
	 * @return
	 */
	private boolean isEmpty(int x, int y){
		return places(x,y).player.equals("NULL");
	}
	
	/**
	 * Highlight tokens by using a pointcut for the drawPlacedCheckers
	 */
	pointcut highlight(Graphics g): execution(void BoardPanel.drawPlacedCheckers(Graphics)) && args(g);

	void around(Graphics g): highlight(g) {
		proceed(g);
		if(cheatEnabled) {
			for(PlayerPlace place : winningPlaces){
				ColorPlayer player = (ColorPlayer) board.playerAt(place.x(), place.y());
				boardPanel.drawChecker(g, player.color(), place.x(), place.y(), true);
			}
		}
	}

	/** Toggles cheatEnabled flag */
	private static void toggleCheat(){
		cheatEnabled = cheatEnabled ? false : true;
	}

	/** Reads key inputs by the user to toggle the cheatEnabled flag */
	@SuppressWarnings("serial")
	private static class KeyAction extends AbstractAction {
		private final BoardPanel boardPanel;

		public KeyAction(BoardPanel boardPanel, String command) {
			this.boardPanel = boardPanel;
			putValue(ACTION_COMMAND_KEY, command);
		}

		/** Called when a cheat is requested. */
		public void actionPerformed(ActionEvent event) {
			toggleCheat();
		}   
	}

	/**
	 * returns PlayerPlace located at x,y coordinate
	 * @param x
	 * @param y
	 * @return
	 */
	public PlayerPlace places(int x, int y) {
		try {
			return places[x][y];
		} catch (Exception e) {
			return null;
		}
	}
	
	pointcut winningRowContains(PlayerPlace p, ArrayList arrayList) : execution(boolean *.contains(..)) && args(p) && target(arrayList);

	boolean around(PlayerPlace p, ArrayList arrayList) : winningRowContains(p, arrayList) {
		for(PlayerPlace place : winningPlaces){
			if(place.player().equals(p.player())
					&& place.x() == p.x() && place.y() == p.y()){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Class that serves as a combination of both
	 * Place and Player classes.
	 */
	private class PlayerPlace extends Place{

		private String player;

		public PlayerPlace(int x, int y) {
			super(x,y);
			this.player = null;
		}

		public PlayerPlace(int x, int y, String player){
			super(x,y);
			this.player = player;
		}

		public String player(){ return player; }
		public int x(){return x;}
		public int y(){return y;}

		public String toString(){
			return "[" + player + "]";
		}
	}

}
