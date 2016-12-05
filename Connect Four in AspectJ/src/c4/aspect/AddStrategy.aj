/**
 * @author Tomas Chagoya, Martin Morales
 */

package c4.aspect;

import java.awt.Color;
import javax.swing.JComboBox;
import javax.swing.JPanel;

import c4.base.C4Dialog;
import c4.ext.Strategy;
import c4.base.BoardPanel;


public privileged aspect AddStrategy {

	private static C4Dialog frame;
	private JComboBox<String> comboBox;
	private JPanel panel;
	private String strategy = "Human";
	private Strategy strat;
	
	pointcut jFrameConstruction(C4Dialog frame) : initialization(C4Dialog.new()) && target(frame);
	after(C4Dialog frame): jFrameConstruction(frame){
		this.frame = frame;
		frame.setSize(265, 360);
		
		String[] options = {"Human", "Smart", "Random"};
		comboBox = new JComboBox<>(options);		
		
		panel = (JPanel)frame.getContentPane().getComponent(0);
		panel.add(comboBox);
		
		strat = new Strategy();
		
		frame.revalidate();
		frame.repaint();
	}
	
	
	/**
	 * Advice and pointcut for button playButton is clicked.
	 * Will set the strategy to what was selected in the
	 * Combo Box
	 */
	pointcut play(): execution(void playButtonClicked(..));	
	void around(): play() {
		strategy = (String)comboBox.getSelectedItem();
		proceed();
	}
	
	/**
	 * Advice and pointcut to intercept and change baseline code
	 * if computer was picked as opponent, with either
	 * Smart or Random
	 */
	pointcut moveMade(): call(void makeMove(int)) && !within(AddStrategy);
	after() returning: moveMade() {
		if(!frame.isGameOver()) {
			if(strategy.equals("Random")) {
				frame.makeMove(strat.random(frame.board));
			}
			else if(strategy.equals("Smart")) {
				frame.makeMove(strat.smart(frame.board));
			}
		}
	}
	 
	pointcut drawingChecker(Color color): call(* BoardPanel.drawChecker(..)) && args(color);
	
	void around(Color color): drawingChecker(color){
		System.out.println("COLOR ME THIS");
	}
}

