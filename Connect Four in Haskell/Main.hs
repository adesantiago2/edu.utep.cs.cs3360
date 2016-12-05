{-
    Author: Martin Morales
    Date: 12/3/16
    Module Description: This module makes up the console-based UI (VC in MVC) for a Connect Four game.
-}

module Main (main) where

import Data.List
import System.IO
import Board
import System.Random

-- ---------- 1 ----------

main :: IO()
-- main function to player Connect Four game on a 7x6 board
main = do
    strategy <- askForStrategy
    let bd = mkBoard 7 6
    gameState <- gameLoop bd mkPlayer strategy    -- Start playing the game, store who won in gameState (refer to gameLoop return values)
    if gameState == 0
        then putStrLn("Game ends in a tie! There's no losers! Or really there are no winners. You both should be ashamed of yourselves.")
        else do
           if gameState == (-1)
                then putStrLn("Player quit the game. What a quitter.")
                else do
                    if gameState == mkPlayer
                        then putStrLn("Player " ++ (show mkPlayer) ++ " won! Congratulations!")
                        else do
                            if gameState == mkOpponent
                                then putStrLn("Player " ++ (show mkOpponent) ++ " won! Congratulations!")
                                else putStrLn("Game ended in an error. This should not be possible.")

askForStrategy :: IO (Int)
-- Asks user for a type of strategy, asks again if input is invalid
askForStrategy = do
    putStrLn "Do you want to human vs. human (1) or human vs. computer (2)?"
    userInput <- getLine
    let parsed = reads userInput :: [(Int, String)]    -- parse input as int
    if length parsed == 0
        then askForStrategy'
        else do
            let (x, _) = head parsed    -- only take first value if multiple int digits passed in
            if x == 1
                then return 1    -- Human vs. human
                else do
                    if x == 2    -- Human vs. computer
                        then return 2
                        else askForStrategy'

askForStrategy' :: IO (Int)
-- helper function to handle invalid inputs for askForStrategy
askForStrategy' = do
    putStrLn "Invalid input!"
    askForStrategy

gameLoop :: [Int] -> Int -> Int -> IO (Int)
-- bd: board, p: player
-- Loops through the game, alternating players, and dropping their token in the slot
-- returns 0 for tie, 1 for player1 win, 2 for player2 win
gameLoop bd p strategy = do
    putStrLn("Player " ++ (show p) ++ "'s turn!")
    if isFull bd
        then return 0    -- tie
        else do
            if strategy == 2 && p == mkOpponent    -- random strategy replaces mkOpponent's readSlot function with genRandomSlot function
                then do
                    slot <- (genRandomSlot bd p)
                    gameLoopHelper bd slot p strategy
                else do
                    slot <- (readSlot bd p)
                    gameLoopHelper bd slot p strategy

gameLoopHelper :: [Int] -> Int -> Int -> Int -> IO (Int)
-- bd: board, slot: column to drop token into, p: player
-- refactored out this code so that I don't have to write it twice for readSlot and genRandomSlot in gameLoop function
gameLoopHelper bd slot p strategy = do
    if slot == (-1)    -- player quit game
        then return (-1)
        else do
            let bdMod = dropInSlot bd slot p
            if isWonBy bdMod mkPlayer
                then do
                    putStrLn(boardToStr playerToChar bdMod)
                    return mkPlayer    -- return 1 for player1 win
                else do
                    if isWonBy bdMod mkOpponent
                        then do
                            putStrLn(boardToStr playerToChar bdMod)
                            return mkOpponent    -- return 2 for player2 win
                        else do
                            putStrLn(boardToStr playerToChar bdMod)
                            if p == mkPlayer    -- alternates players
                                then gameLoop bdMod mkOpponent strategy
                                else gameLoop bdMod mkPlayer strategy


readSlot :: [Int] -> Int -> IO (Int)
-- bd: board, p: player
-- Asks user for a slot input, "loops" through and asks again if input is invalid
-- assumes board is not full
readSlot bd p = do
    putStrLn "Enter a slot position (1-7), -1 to quit: "
    userInput <- getLine
    let parsed = reads userInput :: [(Int, String)]    -- parse input as int
    if length parsed == 0
        then readSlot' bd p
        else do
            let (x, _) = head parsed    -- only take first value if multiple int digits passed in
            if x > 0 && x < ((numSlot bd) + 1) && isSlotOpen bd x   -- 0 < x < 8
                then return x
                else do
                    if x == (-1)
                        then return (-1)
                        else readSlot' bd p

readSlot' :: [Int] -> Int -> IO (Int)
-- bd: board, p: player
-- helper function to handle invalid inputs for readSlot
readSlot' bd p = do
    putStrLn "Invalid input!"
    readSlot bd p

genRandomSlot :: [Int] -> Int -> IO (Int)
-- bd: board, p: player
-- Generates a random slot for the AI to drop a token into
-- assumes board is not full
genRandomSlot bd p = do
    x <- randomRIO(1,7)    -- Generate random number between 1-7 inclusive
    if isSlotOpen bd x
        then return x
        else genRandomSlot bd p

playerToChar :: Int -> Char
-- p: player
-- turns a value of a position in the board to it's respective character
playerToChar p
    | p == mkPlayer = 'X'
    | p == mkOpponent = 'O'
    | otherwise = '.'
