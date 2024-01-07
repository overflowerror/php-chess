<?php

namespace Game;

class Move {
    public Piece $piece;
    public Position $target;
    public ?Piece $captures = null;
    public ?PieceType $promoteTo = null;
    public ?Piece $castleWith = null;

    public function __construct(
        Piece $piece,
        Position $target,
        ?Piece $captures = null,
        ?PieceType $promoteTo = null,
        ?Piece $castleWith = null,
    ) {
        $this->piece = $piece;
        $this->target = $target;
        $this->captures = $captures;
        $this->promoteTo = $promoteTo;
        $this->castleWith = $castleWith;
    }

    public function equals(Move $move): bool {
        return $this->piece->equals($move->piece) &&
            $this->target->equals($move->target) &&
            (
                ($this->captures != null && $move->captures != null && $this->captures->equals($move->captures)) ||
                ($this->captures == null && $move->captures == null)
            ) &&
            $this->promoteTo == $move->promoteTo &&
            (
                ($this->castleWith != null && $move->castleWith != null && $this->castleWith->equals($move->castleWith)) ||
                ($this->castleWith == null && $move->castleWith == null)
            );
    }

    public function getLong(): string {
        if ($this->castleWith) {
            if (abs($this->piece->getPosition()->file - $this->castleWith->getPosition()->file) > 3) {
                return "O-O-O";
            } else {
                return "O-O";
            }
        } else {
            return $this->piece . " " .
                $this->piece->getType()->getShort() . ($this->captures ? "x" : "") . $this->target .
                ($this->promoteTo ? $this->promoteTo->getShort() : "");
        }
    }

    public function __toString(): string {
        return $this->getLong();
    }
}