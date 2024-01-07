<?php

namespace Game;

use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase {

    protected function assertContainsEqualsOnce(object $needle, array $haystack) {
        if (!method_exists($needle, "equals")) {
            $this->assertFalse("equals() missing on needle");
        }

        $result = false;

        foreach ($haystack as $item) {
            if ($needle->equals($item)) {
                if ($result) {
                    $this->assertFalse("element duplication");
                } else {
                    $result = true;
                }
            }
        }

        $this->assertTrue($result, "no such element");
    }

    public function testGameState_illegal_white() {
        $subject = new Game(
            [
                new King(new Position(1, 1), Side::BLACK),
                new Knight(new Position(2, 3), Side::WHITE),
                new King(new Position(7, 6), Side::WHITE),
            ],
            Side::WHITE
        );

        $this->assertEquals(GameState::ILLEGAL, $subject->getGameState());
    }

    public function testGameState_illegal_black() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::WHITE),
                new Queen(new Position(7, 7), Side::BLACK),
                new King(new Position(7, 6), Side::BLACK),
            ],
            Side::BLACK
        );

        $this->assertEquals(GameState::ILLEGAL, $subject->getGameState());
    }

    public function testGameState_check_white() {
        $subject = new Game(
            [
                new King(new Position(5, 4), Side::WHITE),
                new Rook(new Position(2, 4), Side::BLACK),
                new King(new Position(7, 6), Side::BLACK),
            ],
            Side::WHITE
        );

        $this->assertEquals(GameState::CHECK, $subject->getGameState());
    }

    public function testGameState_check_black() {
        $subject = new Game(
            [
                new King(new Position(5, 4), Side::BLACK),
                new Pawn(new Position(4, 3), Side::WHITE),
                new King(new Position(7, 6), Side::WHITE),
            ],
            Side::BLACK
        );

        $this->assertEquals(GameState::CHECK, $subject->getGameState());
    }

    public function testGameState_checkmate_white() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::WHITE),
                new Queen(new Position(1, 1), Side::BLACK),
                new King(new Position(2, 2), Side::BLACK),
            ],
            Side::WHITE
        );

        $this->assertEquals(GameState::CHECKMATE, $subject->getGameState());
    }

    public function testGameState_checkmate_black() {
        $subject = new Game(
            [
                new King(new Position(0, 4), Side::BLACK),
                new Rook(new Position(0, 1), Side::WHITE),
                new King(new Position(2, 4), Side::WHITE),
            ],
            Side::BLACK
        );

        $this->assertEquals(GameState::CHECKMATE, $subject->getGameState());
    }

    public function testGameState_stalemate_white() {
        $subject = new Game(
            [
                new King(new Position(3, 4), Side::WHITE),
                new Pawn(new Position(3, 3), Side::WHITE),
                new Rook(new Position(3, 2), Side::BLACK),
                new King(new Position(3, 6), Side::BLACK),
                new Queen(new Position(4, 7), Side::BLACK),
                new Knight(new Position(0, 2), Side::BLACK),
                new Bishop(new Position(1, 3), Side::BLACK),
            ],
            Side::WHITE
        );

        $this->assertEquals(GameState::STALEMATE, $subject->getGameState());
    }

    public function testGameState_stalemate_black() {
        $subject = new Game(
            [
                new King(new Position(0, 7), Side::BLACK),
                new Rook(new Position(1, 1), Side::WHITE),
                new King(new Position(0, 5), Side::WHITE),
            ],
            Side::BLACK
        );

        $this->assertEquals(GameState::STALEMATE, $subject->getGameState());
    }


    public function testGameState_threeFoldRepetition_black() {
        $subject = new Game(
            [
                new King(new Position(1, 1), Side::BLACK, true),
                new King(new Position(7, 6), Side::WHITE, true),
            ],
            Side::BLACK
        );
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(1, 1), Side::BLACK),
            new Position(1, 2),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 6), Side::WHITE),
            new Position(7, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(1, 2), Side::BLACK),
            new Position(1, 1),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 7), Side::WHITE),
            new Position(7, 6),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(1, 1), Side::BLACK),
            new Position(1, 2),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 6), Side::WHITE),
            new Position(7, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(1, 2), Side::BLACK),
            new Position(1, 1),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 7), Side::WHITE),
            new Position(7, 6),
        ));

        $this->assertEquals(GameState::THREEFOLD_REPETITION, $subject->getGameState());
    }


    public function testGameState_noThreeFoldRepetitionWithCastlingRights_black() {
        $subject = new Game(
            [
                new King(new Position(4, 7), Side::BLACK, false),
                new Rook(new Position(0, 7), Side::BLACK, false),
                new King(new Position(7, 6), Side::WHITE, true),
            ],
            Side::BLACK
        );
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(4, 7), Side::BLACK),
            new Position(3, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 6), Side::WHITE),
            new Position(7, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(3, 7), Side::BLACK),
            new Position(4, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 7), Side::WHITE),
            new Position(7, 6),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(4, 7), Side::BLACK),
            new Position(3, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 6), Side::WHITE),
            new Position(7, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(3, 7), Side::BLACK),
            new Position(4, 7),
        ));
        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());

        $subject->applyInPlace(new Move(
            new King(new Position(7, 7), Side::WHITE),
            new Position(7, 6),
        ));

        $this->assertEquals(GameState::DEFAULT, $subject->getGameState());
    }

    public function testLegalMoves_pawnPinnedBecauseOfCheckKingRestrictedByQueenAndPawn() {
        $subject = new Game(
            [
                new King(new Position(7, 6), Side::BLACK),
                new Queen(new Position(1, 6), Side::BLACK),
                new Pawn(new Position(2, 6), Side::WHITE),
                new King(new Position(3, 6), Side::WHITE),
            ],
            Side::WHITE
        );

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(5, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(3, 6), Side::WHITE),
            new Position(3, 7),
            null, null,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(3, 6), Side::WHITE),
            new Position(4, 7),
            null, null,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(3, 6), Side::WHITE),
            new Position(4, 6),
            null, null,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(3, 6), Side::WHITE),
            new Position(4, 5),
            null, null,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(3, 6), Side::WHITE),
            new Position(3, 5),
            null, null,
        ), $legalMoves);
    }

    public function testLegalMoves_kingIsBlockedPawnCanPromote() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::BLACK),
                new King(new Position(3, 6), Side::WHITE),
                new Queen(new Position(1, 2), Side::WHITE),
                new Pawn(new Position(7, 1), Side::BLACK, true),
            ],
            Side::BLACK
        );

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(4, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(7, 1), Side::BLACK),
            new Position(7, 0),
            null, PieceType::BISHOP,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(7, 1), Side::BLACK),
            new Position(7, 0),
            null, PieceType::KNIGHT,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(7, 1), Side::BLACK),
            new Position(7, 0),
            null, PieceType::ROOK,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(7, 1), Side::BLACK),
            new Position(7, 0),
            null, PieceType::QUEEN,
        ), $legalMoves);
    }

    public function testLegalMoves_kingIsBlockedInitialPawnMove() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::BLACK),
                new King(new Position(3, 6), Side::WHITE),
                new Queen(new Position(1, 2), Side::WHITE),
                new Pawn(new Position(1, 6), Side::BLACK),
            ],
            Side::BLACK
        );

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(2, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(1, 6), Side::BLACK),
            new Position(1, 5),
            null, null,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(1, 6), Side::BLACK),
            new Position(1, 4),
            null, null,
        ), $legalMoves);
    }

    public function testLegalMoves_kingIsBlockedEnPassant() {
        $opponentPawn = new Pawn(new Position(3, 1), Side::WHITE);
        $opponentPawn->move(new Position(3, 3));

        $subject = new Game(
            [
                new King(new Position(0, 0), Side::BLACK),
                new King(new Position(7, 6), Side::WHITE),
                new Queen(new Position(1, 2), Side::WHITE),
                $opponentPawn,
                new Pawn(new Position(4, 3), Side::BLACK, true),
            ],
            Side::BLACK
        );

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(2, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(4, 3), Side::BLACK),
            new Position(4, 2),
            null, null,
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(4, 3), Side::BLACK),
            new Position(3, 2),
            $opponentPawn, null,
        ), $legalMoves);
    }

    public function testLegalMoves_kingIsInCheckAttackerCanBeTaken() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::BLACK),
                new King(new Position(3, 6), Side::WHITE),
                new Queen(new Position(1, 1), Side::WHITE),
                new Queen(new Position(5, 1), Side::BLACK),
            ],
            Side::BLACK
        );

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(2, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(0, 0), Side::BLACK),
            new Position(1, 1),
            new Queen(new Position(1, 1), Side::WHITE), null,
        ), $legalMoves);
    }

    public function testLegalMoves_enPassantNotPossibleBecauseMoveInBetween() {
        $subject = new Game(
            [
                new King(new Position(0, 1), Side::BLACK),
                new King(new Position(0, 7), Side::WHITE),
                new Queen(new Position(1, 3), Side::WHITE),
                new Pawn(new Position(5, 1), Side::WHITE),
                new Pawn(new Position(6, 2), Side::WHITE),
                new Pawn(new Position(6, 3), Side::BLACK, true),
            ],
            Side::WHITE
        );
        $subject->applyInPlace(new Move(
            new Pawn(new Position(5, 1), Side::WHITE),
            new Position(5, 3)
        ));
        $subject->applyInPlace(new Move(
            new King(new Position(0, 1), Side::BLACK),
            new Position(0, 0)
        ));
        $subject->applyInPlace(new Move(
            new Queen(new Position(1, 3), Side::WHITE),
            new Position(1, 4)
        ));

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(1, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(0, 0), Side::BLACK),
            new Position(0, 1),
        ), $legalMoves);
    }

    public function testLegalMoves_enPassantNotPossibleBecausePawnDidntMove2Squares() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::BLACK),
                new King(new Position(0, 7), Side::WHITE),
                new Queen(new Position(1, 3), Side::WHITE),
                new Pawn(new Position(5, 1), Side::WHITE),
                new Pawn(new Position(6, 2), Side::WHITE),
                new Pawn(new Position(6, 3), Side::BLACK, true),
            ],
            Side::WHITE
        );
        $subject->applyInPlace(new Move(
            new Pawn(new Position(5, 1), Side::WHITE),
            new Position(5, 2)
        ));
        $subject->applyInPlace(new Move(
            new King(new Position(0, 0), Side::BLACK),
            new Position(0, 1)
        ));
        $subject->applyInPlace(new Move(
            new Pawn(new Position(5, 2), Side::WHITE),
            new Position(5, 3)
        ));

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(1, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(0, 1), Side::BLACK),
            new Position(0, 0),
        ), $legalMoves);
    }

    public function testLegalMoves_enPassantPossible() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::BLACK),
                new King(new Position(0, 7), Side::WHITE),
                new Queen(new Position(1, 2), Side::WHITE),
                new Pawn(new Position(5, 1), Side::WHITE),
                new Pawn(new Position(6, 2), Side::WHITE),
                new Pawn(new Position(6, 3), Side::BLACK, true),
            ],
            Side::WHITE
        );

        $subject->applyInPlace(new Move(
            new Pawn(new Position(5, 1), Side::WHITE),
            new Position(5, 3)
        ));

        $legalMoves = $subject->getLegalMoves();

        $this->assertCount(1, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Pawn(new Position(6, 3), Side::BLACK),
            new Position(5, 2),
            new Pawn(new Position(5, 3), Side::WHITE),
        ), $legalMoves);
    }

    public function testLegalMoves_castle() {
        $subject = new Game(
            [
                new King(new Position(0, 7), Side::BLACK),
                new King(new Position(4, 0), Side::WHITE),
                new Queen(new Position(2, 1), Side::BLACK),
                new Rook(new Position(7, 0), Side::WHITE),
                new Rook(new Position(7, 1), Side::BLACK),
            ],
            Side::WHITE
        );

        $legalMoves = $subject->getLegalMoves();

        echo join("\n", $legalMoves);

        $this->assertCount(5, $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(4, 0), Side::BLACK),
            new Position(5, 0),
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new King(new Position(4, 0), Side::BLACK),
            new Position(6, 0),
            null,
            null,
            new Rook(new Position(7, 0), Side::WHITE),
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Rook(new Position(7, 0), Side::WHITE),
            new Position(6, 0),
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Rook(new Position(7, 0), Side::WHITE),
            new Position(5, 0),
        ), $legalMoves);

        $this->assertContainsEqualsOnce(new Move(
            new Rook(new Position(7, 0), Side::WHITE),
            new Position(7, 1),
            new Rook(new Position(7, 1), Side::BLACK),
        ), $legalMoves);
    }

    public function testApply_castles() {
        $subject = new Game(
            [
                new King(new Position(0, 7), Side::BLACK),
                new King(new Position(4, 0), Side::WHITE),
                new Rook(new Position(7, 0), Side::WHITE),
            ],
            Side::WHITE
        );

        $subject->applyInPlace(new Move(
            new King(new Position(4, 0), Side::WHITE),
            new Position(6, 0),
            null,
            null,
            new Rook(new Position(7, 0), Side::WHITE),
        ));

        $this->assertEquals(Side::BLACK, $subject->getCurrentSide());

        $pieces = $subject->getPieces(Side::WHITE);
        $this->assertCount(2, $pieces);

        $this->assertContainsEqualsOnce(
            new King(new Position(6, 0), Side::WHITE),
            $pieces
        );
        $this->assertContainsEqualsOnce(
            new Rook(new Position(5, 0), Side::WHITE),
            $pieces
        );

    }
}