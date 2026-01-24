<?php

namespace TnFAT\Planner;

interface EntityFormatter {
    public function setHeader(): void;
    /**
     * Reads the data using the associated reader and stores it for.
     * @param ?string $id of the entity to read, or null to read all entities
     * @return string
     */
    public function read(?string $id): string;
    /**
     * Formats the data read using the read method intp the desired output format.
     * @return string
     */
    public function format(): string;
}