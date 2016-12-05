package c4.ext;

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

public privileged aspect AddCheatKey {
	
	private static boolean cheatEnabled = false;

	private Board board;
	private BoardPanel boardPanel;
	private PlayerPlace[][] places;
	private List<PlayerPlace> winningPlaces = new ArrayList<PlayerPlace>();
	
	pointcut boardConst(Board b) : initialization(Board.new()) && target(b);
	
	after(Board b) : boardConst(b) {
		board = b;
		places = new PlayerPlace[b.numOfSlots()][b.slotHeight()];
	}
	
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
    	
    	/* For debugging */
//		for(int i = 0; i < places.length; i++){
//			for(int j = 0; j < places[i].length; j++) {
//				System.out.println(places[i][j]+"("+places[i][j].x()+ "," + places[i][j].y() + ")");
//			}
//		}
	}
	
	/** Scans board for tokens in rows/cols/diag that have three tokens in a row of the same player
	 *  and an empty place that can be inserted into
	 */
	private void scanBoard() {
		for(PlayerPlace[] placeRow : places)
    		for(PlayerPlace placeCol : placeRow) {
        		scanHorizontal(placeCol);
        		scanVertical(placeCol);
        		scanDiagonalOne(placeCol);
        		scanDiagonalTwo(placeCol);
    		}
	}
	
  private boolean scanHorizontal(PlayerPlace place){
	int inARow = 1;
	
	if(place.player().equals("NULL")) return false;
	int x = place.x;
	int y = place.y;

	//checks from left to right
	for(int offset = 1; offset < 4; offset++) {
		if(places(x + offset, y) != null && place.player().equals(places(x + offset, y).player())) {
			inARow++;
		}
		else {
			break;
		}
	}
	
	if(inARow == 3) {
		System.out.println("places(x + 4, y) name: " + places(x + 3, y).player() + " @ x: " + (x + 3) + " @ y: " + y);
		if(places(x + 3, y) != null && places(x + 3, y).player().equals("NULL")){
			
			for(int offset = 0; offset < 3; offset++) {		// Add winning row to winningPlaces Arraylist
				//TODO: THE CONTAINS METHOD IS NOT WORKING CORRECTLY
//				System.out.println("ALL WINNING PLACES");
//				
//				for(PlayerPlace winningPlace : winningPlaces){
//					System.out.println(winningPlace.player() + " @ x: " + winningPlace.x() + ", y: " + winningPlace.y());
//				}
//
//				System.out.println("Check if it already contains: " + places(x + offset, y).player() + " @ x: " + (x + offset) + ", y: " + y + " value: " + winningPlaces.contains(places(x + offset, y)));
				
				if(!winningPlaces.contains(places(x + offset, y)))	// only if they aren't already in winningPlaces
					winningPlaces.add(places(x + offset, y));
			}
			
			return true;
		}
	}
		
		
	return false;
}
  private boolean scanVertical(PlayerPlace place){
	  //TODO implement method
  	return false;
  }
  
  private boolean scanDiagonalOne(PlayerPlace place){
	  //TODO implement method
  	return false;
  }
  
  private boolean scanDiagonalTwo(PlayerPlace place){
	  //TODO implement method
  	return false;
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
		
		
		
		public boolean equals(PlayerPlace playerPlace){
			return (this.player == playerPlace.player() && this.player != null);
		}
		
		public String toString(){
			return "[" + player + "]";
		}
		
//		public boolean equals(PlayerPlace otherPlace) {
//			boolean equals = true;
//			if(!player.equals(otherPlace.player))
//				return false;
//			if(x() != otherPlace.x())
//				return false;
//			if(y() != otherPlace.y())
//				return false;
//			return true;
//		}
    	
    }
	
}
