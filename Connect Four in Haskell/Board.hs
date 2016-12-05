{-
    Author: Martin Morales
    Date: 12/3/16
    Module Description: This module makes up the game model (M in MVC) for a Connect Four game.
-}

module Board (mkBoard, mkPlayer, dropInSlot, mkOpponent, isSlotOpen, numSlot, isFull, isWonBy, boardToStr) where

import Data.List

-- ---------- 1 ----------

mkBoard :: Int -> Int -> [Int]
-- m: cols, n: rows
-- returns 1-based index, board is represented as list [c1r1, c2r1, ... cmr1, c1r2, c2r2, ... cmrn]
-- NOTE: first value of board is the number of cols
mkBoard m n = m : take (m*n) (repeat (-1))

mkPlayer :: Int
mkPlayer = 1
mkOpponent :: Int
mkOpponent = 2

-- ---------- 2 ----------

dropInSlot :: [Int] -> Int -> Int -> [Int]
-- bd: board, i: col/slot,  p: player 
-- Drops player p's disc into a column of the board. Slot is assumed to have an empty space.
-- returns modified board
dropInSlot bd i p
    -- if value at top position of col is empty and the position underneath, recursive call on position underneath
    | bd !! i == -1 && i + (numSlot bd) < length bd &&  bd !! (i + (numSlot bd)) == -1  = dropInSlot bd (i+(numSlot bd)) p
    -- We assumed there is an open spot, and there are no open spots beneath current position. Place player at i
    | otherwise = take i bd ++ [p] ++ drop (i + 1) bd

isSlotOpen :: [Int] -> Int -> Bool
-- bd: board, i: col/slot
-- does the column have at least one open space?
isSlotOpen bd i = bd !! i == -1    -- Is top spot of col empty

numSlot :: [Int] -> Int
-- bd: board
-- returns number of columns in board
numSlot bd = bd !! 0

isFull :: [Int] -> Bool
-- bd: board
-- Returns if board is full player p?
isFull bd = not(-1 `elem` bd)    -- Check if there are any empty spots (-1) in bd

-- ---------- 3 ----------

isWonBy :: [Int] -> Int -> Bool
isWonVert :: [Int] -> Int -> Bool
isWonHor :: [Int] -> Int -> Bool
isWonDiagTL :: [Int] -> Int -> Bool
isWonDiagTR :: [Int] -> Int -> Bool
-- bd: board, p: player
-- Is the game played on board bd won 
isWonBy bd p = isWonVert bd p || isWonHor bd p || isWonDiagTL bd p || isWonDiagTR bd p

isWonVert bd p
    | length bd == 1 = False    -- only numSlot value left
    | head (tail bd) == p && (numSlot bd) * 3 + 1 < length bd    -- preserve numSlot value && check that last row will be in the board
    && bd !! ((numSlot bd) + 1) == p
    && bd !! ((numSlot bd) * 2 + 1) == p
    && bd !! ((numSlot bd) * 3 + 1) == p
    = True
    | otherwise = isWonVert ([head bd] ++ (drop 2 bd)) p    -- preserves numSlot value, gets rid of next value (traverse through list)

isWonHor bd p
    | length bd == 1 = False    -- only numSlot value left
    | head (tail bd) == p && 4 < length bd    -- preserve numSlot value && check that next four positions will be in the board
    && bd !! 2 == p
    && bd !! 3 == p
    && bd !! 4 == p
    = True
    | otherwise = isWonHor ([head bd] ++ (drop 2 bd)) p    -- preserves numSlot value, gets rid of next value (traverse through list)

isWonDiagTL bd p
    | length bd == 1 = False    -- only numSlot value left
    | head (tail bd) == p && (3 * (numSlot bd) + 4) < length bd    -- preserve numSlot value && check that lowest position in diag will be in the board
    && bd !! ((numSlot bd) + 2) == p
    && bd !! (2 * (numSlot bd) + 3) == p
    && bd !! (3 * (numSlot bd) + 4) == p
    = True
    | otherwise = isWonDiagTL ([head bd] ++ (drop 2 bd)) p    -- preserves numSlot value, gets rid of next value (traverse through list)

isWonDiagTR bd p
    | length bd == 1 = False    -- only numSlot value left
    | head (tail bd) == p && ((3 * ((numSlot bd) - 1)) + 1) < length bd    -- preserve numSlot value && check that lowest position in diag will be in the board
    && bd !! (numSlot bd) == p
    && bd !! ((2 * ((numSlot bd) - 1)) + 1) == p
    && bd !! ((3 * ((numSlot bd) - 1)) + 1) == p
    = True
    | otherwise = isWonDiagTR ([head bd] ++ (drop 2 bd)) p    -- preserves numSlot value, gets rid of next value (traverse through list)

-- ---------- 4 ----------

boardToStr :: (Int -> Char) -> [Int] -> String
-- playerToChar: turns an int into a char, bd: board
-- takes a board and returns a string of the board to display
boardToStr playerToChar bd
    | length bd == 1 = ""
    -- same as otherwise, except it adds in \n chars in after the number of columns
    | length bd `mod` (numSlot bd) == 2 = [playerToChar (head (tail bd))] ++ "\n" ++ boardToStr playerToChar ([head bd] ++ (drop 2 bd))
    -- Skip over 0th element (numSlot), translate current int into char and append that, and append recursive call to rest of the board
    | otherwise = [playerToChar (head (tail bd))] ++ boardToStr playerToChar ([head bd] ++ (drop 2 bd))
