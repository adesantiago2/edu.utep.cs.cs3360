package c4.ext;

import c4.base.C4Dialog;

public privileged aspect AgainstUI {
	
	after(C4Dialog dialog): this(dialog)
	&& execution(void C4Dialog.configureUI()) {
		/*
		strategyPane = new StrategyPanel();
		strategyPane.setStrategies(
				new String[] {"Human", "Random", "Smart"});
		dialog.playButton.getParent().add(strategyPane);
		setStrategy(dialog.board, dialog.opponent());
		*/
	}
}