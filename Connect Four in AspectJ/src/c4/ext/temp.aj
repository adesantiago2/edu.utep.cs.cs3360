//import java.awt.event.ActionEvent;
//import java.awt.event.KeyEvent;
//import java.util.ArrayList;
//import java.util.List;
//
//import javax.swing.AbstractAction;
//import javax.swing.ActionMap;
//import javax.swing.InputMap;
//import javax.swing.JComponent;
//import javax.swing.KeyStroke;
//
//import c4.base.BoardPanel;
//import c4.base.BoardPanel.Game;
//import c4.model.Board;
//import c4.model.Player;
//import c4.model.Board.Place;
//
//privileged aspect AddCheatKey {
//	
//	private static boolean cheatEnabled = false;
//	
//	
//	private Board board;
//	private BoardPanel boardPanel;
//	private List<List<Place>> cheatingRows = new ArrayList<>();	// List of all of the rows, each row is a list of places
//	
//	/** Intercept and save board when the Board constructor is called */
////	after(Board b) : execution(Board.new()) && target(b){
////		board = b;
////	}
//	
//	pointcut boardConst() : initialization(Board.new();
//	
//	after() : boardConst() {
//		//TODO
//	}
//	
////	/** Intercept and save BoardPanel when the BoardPanel constructor is called
////	* Then initialize the ActionMap and InputMap needed to read the F5 keystroke for the cheat key
////	*/
////	after(BoardPanel bp) : initialization(BoardPanel.new(Board, Game)) && target(bp) {
////		ActionMap actionMap = bp.getActionMap();
////        int condition = JComponent.WHEN_IN_FOCUSED_WINDOW;
////        InputMap inputMap = bp.getInputMap(condition);
////        String cheat = "Cheat";
////        inputMap.put(KeyStroke.getKeyStroke(KeyEvent.VK_F5, 0), cheat);
////        actionMap.put(cheat, new KeyAction(bp, cheat));
////        
////        boardPanel = bp;
////	}
//	
//	/** Pointcut to change the color of the tokens any time a token is dropped or the cheat key is pressed */
//	after(): call(int Board.dropInSlot(int, Player)) || execution(void KeyAction.actionPerformed(ActionEvent)) {
//		getPlaces();
//		//printPlaces();
//		scanBoard();
//		//highlightTokens();
//		printWinningPlaces();
//		
//	}
//    
//	/** Scans board for tokens in rows/cols/diag that have three tokens in a row of the same player
//     *  and an empty place that can be inserted into
//	 */
////    private void scanBoard() {
////    	if(board == null) return;
////    	final int CHEATING_SLOTS = 4;	// 3 slots in the row is almost winning, the fourth place needs to be empty
////    	
////    	List<Place> tempRow = new ArrayList();
////    	for(int row = 0; row < board.slotHeight() - 3; row++) {
////    		for(int col = board.numOfSlots(); col > 2; col--) {
////    			Player player = board.places[row][col];
////    			tempRow.add(new Place(col, row));
////    			
////    			// Vertical
////    			for(int adjacentPlace = 1; adjacentPlace < CHEATING_SLOTS; adjacentPlace++) {
////    				if(board.isOccupiedBy(col, row - adjacentPlace, player)) {
////    					tempRow.add(new Place(col, adjacentPlace));
////    				} else {
////    					break;
////    				}
////    			}
////    			if(tempRow.size() == 4) {
////    				cheatingRows.add(tempRow);
////    				break;
////    			}
////    			
////    			tempRow.removeAll(tempRow);
////    			// Horizontal
////    			
////    		}
////    	}
////    }
//    
//    private PlayerPlace[][] places = new PlayerPlace[board.numOfSlots()][board.slotHeight()];
//    private List<PlayerPlace> winningPlaces = new ArrayList<>();
//    
//    private void getPlaces(){
//    	if(board == null) return;
//    	
//    	for(int y = board.slotHeight()-1; y >= 0; y--){
//    		for(int x = 0; x < board.numOfSlots(); x++){
//    			Player player = board.playerAt(x, y);
//    			String p = "";
//    			
//    			if(player == null)
//    				p = "NULL";
//    			else
//    				p = player.name();
//    			
//    			places[x][y] = new PlayerPlace(x,y,p);
//    		}
//    	}
//    	
////    	for(int i = 0; i < places.length; i++){
////    		System.out.println(places[i]+"("+places[i].x()+ "," + places[i].y() + ")");
////    	}
//    }
//    
//    /** Helper method; only for debugging **/
//    private void printWinningPlaces(){
//    	System.out.println("PRINTING WINNING ROWS");
//    	for(PlayerPlace place : winningPlaces)
//    		System.out.println(place);
//    }
//    
////    private void printPlaces(){
////    	for(PlayerPlace place: places){
////    		System.out.println(place);
////    	}
////    }
//    
//    private void scanBoard(){
//    	for(PlayerPlace[] placeRow : places)
//    		for(PlayerPlace placeCol : placeRow)
//        		if(scanHorizontal(placeCol))
//        			winningPlaces.add(placeCol);
//    }
//    
//    private boolean scanHorizontal(PlayerPlace place){
//    	int inARow = 0;
//    	
////    	int stop = place.x() + 4;
////    	int offset;
////    	//Going right first
////    	for(offset = place.x() + 1; offset < stop; offset++){
////    		
////			System.out.println("Place.player " + place.player() + ", places[row].player " + places[offset].player() + " offset " + offset);
////			
////    		if(!place.player().equals("NULL") && place.player.equals(places[offset].player))
////    		{
////    			inARow++;
////    		}
////    		else
////    			break;
////    	}
////    	
////    	if(places[offset+1].player().equals("NULL") && inARow == 3)	// potential index out of bounds
////    		return true;
//    	
//    	if(place.player().equals("NULL")) return false;
//    	int x = place.x;
//    	int y = place.y;
//
//    	System.out.println("PLACE.PLAYER " + place.player());
//    	
//    	for(int offset = 1; offset < 4; offset++) {
//    		System.out.println("Offset place: " + places[x + offset][y]);
//    		if(place.player().equals(places[x + offset][y])) {
//    			inARow++;
//    		}
//    		else {
//    			break;
//    		}
//    	}
//    	
//    	if(inARow == 4)
//    		return true;
//    		
//    	return false;
//    }
//    
//    private boolean scanVertical(PlayerPlace place){
//    	return false;
//    }
//    
//    private boolean scanDiagonalOne(PlayerPlace place){
//    	return false;
//    }
//    
//    private boolean scanDiagonalTwo(PlayerPlace place){
//    	return false;
//    }
//    
//    /** Highlights rows that are close to winning, found by scanBoard()
//     */
//    private void highlightTokens() {
////    	if(boardPanel == null) return;
////    	
////
////    	for(int i = 0; i < cheatingRows.size(); i++) {
////    		boardPanel.drawChecker()
////    	}
//    }
//    
//    /** Toggles cheatEnabled flag */
//    private static void toggleCheat(){
//    	cheatEnabled = cheatEnabled ? false : true;
//    }
//    
//    /** Reads key inputs by the user to toggle the cheatEnabled flag */
//    @SuppressWarnings("serial")
//    private static class KeyAction extends AbstractAction {
//       private final BoardPanel boardPanel;
//       
//       public KeyAction(BoardPanel boardPanel, String command) {
//           this.boardPanel = boardPanel;
//           putValue(ACTION_COMMAND_KEY, command);
//       }
//       
//       /** Called when a cheat is requested. */
//       public void actionPerformed(ActionEvent event) {
//    	   toggleCheat();
//       }   
//    }
//    
//    private class PlayerPlace extends Place{
//
//    	private String player;
//    	
//		public PlayerPlace(int x, int y) {
//			super(x,y);
//			this.player = null;
//		}
//		
//		public PlayerPlace(int x, int y, String player){
//			super(x,y);
//			this.player = player;
//		}
//		
//		public String player(){ return player; }
//		public int x(){return x;}
//		public int y(){return y;}
//		
//		public boolean equals(PlayerPlace playerPlace){
//			return (this.player == playerPlace.player() && this.player != null);
//		}
//		
//		public String toString(){
//			return "[" + player + "]";
//		}
//    	
//    }
//    
//   
//}
