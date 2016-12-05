package c4.ext;

import java.io.IOException;

import javax.sound.sampled.AudioInputStream;
import javax.sound.sampled.AudioSystem;
import javax.sound.sampled.Clip;
import javax.sound.sampled.LineUnavailableException;
import javax.sound.sampled.UnsupportedAudioFileException;

import c4.base.C4Dialog;
import c4.model.Board;
import c4.model.Player;

public aspect AddSound {
	
	pointcut tokenDropped(int slot, Player player): call(int Board.dropInSlot(int, Player)) && args(slot, player);
	
	int around(int slot, Player player): tokenDropped(slot, player) {
		int val = proceed(slot, player);
		
		if(val != -1) {
			if(player.name().equals("Red"))
				playAudio("waterdrop.wav");
			if(player.name().equals("Blue"))
				playAudio("wilhelm.wav");
		}
		return val;
	}
	
	pointcut victory(): call(boolean Board.isWonBy(*));
	
	boolean around(): victory(){
		boolean isWin = proceed();
		
		if(isWin)
			playAudio("winning.wav");
		
		return isWin;
	}
	
	pointcut newGameStarts(): call(void C4Dialog.startNewGame()); 
	
	before(): newGameStarts(){
		stopAudio();
	}
	
	
	/** Directory where audio files are stored. */
    private static final String SOUND_DIR = "/sounds/";
    
    /** Clip that holds the current audio to be played; class scope for stopping sound after starting a new game **/
    private static Clip clip;

    private static void playAudio(String filename) {
    	try {
    		AudioInputStream audioIn = AudioSystem.getAudioInputStream(
    				AddSound.class.getResource(SOUND_DIR + filename));
    		clip = AudioSystem.getClip();
    		clip.open(audioIn);
    		clip.start();     
    	} catch (UnsupportedAudioFileException | IOException | LineUnavailableException e) {
    		e.printStackTrace();
    	}
    }
    
    private static void stopAudio(){
    	clip.stop();
    }
}
