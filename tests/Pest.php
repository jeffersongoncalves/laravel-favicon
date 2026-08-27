<?php

use JeffersonGoncalves\Favicon\Tests\TestCase;

// TestCase::setUp() swaps the Vite resolver for a deterministic fake, so the
// mock lives there as the single source of truth — no per-suite beforeEach.
uses(TestCase::class)->in(__DIR__);
