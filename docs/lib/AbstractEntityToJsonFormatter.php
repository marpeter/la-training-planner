<?php

namespace TnFAT\Planner;

abstract class AbstractEntityToJsonFormatter implements EntityFormatter {

    /**
     * override @var reader/construct to hold the reader for this formatter's Entity
     */
    protected AbstractDatabaseTable $reader;
    protected $data = [];

    public function setHeader(): void {
        header('Content-Type: application/json');
    }

    public function read(?string $id): string {
        $this->setHeader();
        $this->data = $this->reader->read($id);
        return $this->format();
    }
}