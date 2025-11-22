<?php

declare(strict_types=1);

/**
 * ============================================================
 * Dictionary for SGuverLampTuya2 control (English)
 * ============================================================
 *
 * This file defines $dictionary array for recognizing text or voice commands
 * for controlling SGuverLampTuya2 devices:
 *
 *   - Brightness control (white light)
 *   - Color temperature control
 *   - Color control (RGB)
 *   - Color brightness control (RGB)
 *   - Scene control
 *
 * Each key in the $dictionary represents a category of commands.
 * Values are strings with keywords separated by | (OR) used for matching.
 *
 * For each key, a constant LANG_<KEY> is automatically defined, e.g.:
 *   LANG_SGuverLampTuya2_PATTERN_BRIGHTNESS
 *
 * These constants are used in command parsing logic to match user input.
 *
 * ------------------------------------------------------------------------
 * @phpstan-type PatternKey
 *     'SGuverLampTuya2_PATTERN_BRIGHTNESS' |
 *     'SGuverLampTuya2_PATTERN_TEMPERATURE' |
 *     'SGuverLampTuya2_PATTERN_COLOR' |
 *     'SGuverLampTuya2_PATTERN_COLOR_BRIGHTNESS' |
 *     'SGuverLampTuya2_PATTERN_SCENE'
 *
 * @phpstan-type PatternDictionary array<PatternKey, non-empty-string>
 *
 * @var PatternDictionary $dictionary
 */

$dictionary = [

    /**
     * Brightness control (white light)
     * Examples: "bright", "brightness", "increase light", "decrease light"
     * @var non-empty-string
     */
    'SGuverLampTuya2_PATTERN_BRIGHTNESS' =>
        'bright|brightness|lighter|dimmer|light level|level|increase light|decrease light',

    /**
     * Color temperature control
     * Examples: "temperature", "warm", "cool", "cold", "neutral", "tone", "white", "yellow", "blue"
     * @var non-empty-string
     */
    'SGuverLampTuya2_PATTERN_TEMPERATURE' =>
        'temperature|color|warm|cool|cold|neutral|tone|white|yellow|blue',

    /**
     * Basic color control (RGB)
     * Examples: "red", "green", "blue", "white", "yellow", "cyan", "magenta", "orange", "purple", "pink", "lime"
     * @var non-empty-string
     */
    'SGuverLampTuya2_PATTERN_COLOR' =>
        'red|green|blue|white|yellow|cyan|magenta|orange|purple|pink|lime',

    /**
     * Color brightness control (RGB)
     * Examples: "color brighter", "increase color brightness", "dim color", "decrease color"
     * @var non-empty-string
     */
    'SGuverLampTuya2_PATTERN_COLOR_BRIGHTNESS' =>
        'color brighter|increase color|increase rgb|brighter color|dim color|decrease color|decrease rgb',

    /**
     * Scene control
     * Examples: "scene", "mode", "setting"
     * @var non-empty-string
     */
    'SGuverLampTuya2_PATTERN_SCENE' =>
        'scene|mode|setting|'
];

/**
 * Define constants LANG_<KEY> for each pattern if not already defined.
 *
 * @phpstan-param PatternDictionary $dictionary
 */
foreach ($dictionary as $key => $pattern) {

    /** @var string $constName */
    $constName = 'LANG_' . $key;

    if (!defined($constName)) {
        /** @psalm-suppress RedundantErrorControl */
        @define($constName, $pattern);
    }
}
