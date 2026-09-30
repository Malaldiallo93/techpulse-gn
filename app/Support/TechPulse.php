<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

/**
 * Libellés et règles partagés : thématiques, niveaux, dates et comptes à rebours.
 */
class TechPulse
{
    public const THEMES = [
        'ia' => ['label' => 'IA', 'long' => 'Intelligence artificielle'],
        'cyber' => ['label' => 'Cyber', 'long' => 'Cybersécurité'],
        'data' => ['label' => 'Data', 'long' => 'Data'],
        'opp' => ['label' => 'Opportunités', 'long' => 'Opportunités'],
    ];

    public const LEVELS = [1 => 'Débutant', 2 => 'Intermédiaire', 3 => 'Avancé'];

    public const COUNTRIES = ['Guinée', 'Sénégal', 'Mali', 'Côte d’Ivoire'];

    public const CITIES = ['Conakry', 'Kankan', 'Labé', 'En ligne'];

    /** Seuil d'urgence des comptes à rebours, en heures. */
    public const URGENT_HOURS = 48;

    public static function themeLabel(string $theme): string
    {
        return self::THEMES[$theme]['label'] ?? Str::upper($theme);
    }

    public static function themeLong(string $theme): string
    {
        return self::THEMES[$theme]['long'] ?? $theme;
    }

    public static function levelLabel(int $level): string
    {
        return self::LEVELS[$level] ?? self::LEVELS[1];
    }

    public static function levelFromLabel(string $label): int
    {
        $i = array_search($label, self::LEVELS, true);

        return $i === false ? 1 : $i;
    }

    /** « 30 sept. 2026 » */
    public static function shortDate(CarbonInterface $d, bool $year = true): string
    {
        return $d->locale('fr')->translatedFormat($year ? 'j M Y' : 'j M');
    }

    /** « 8 h 10 » */
    public static function time(CarbonInterface $d): string
    {
        return $d->format('G').' h '.$d->format('i');
    }

    /**
     * Compte à rebours au format de la charte : « 6 j 14 h », puis « 19 h 40 » sous 24 h.
     * Recalculé côté client toutes les minutes (voir data-deadline).
     *
     * @return array{txt:string,big:string,small:string,full:string,urgent:bool,closed:bool}
     */
    public static function countdown(CarbonInterface $deadline, ?CarbonInterface $now = null): array
    {
        $now ??= now();
        $ms = ($deadline->getTimestamp() - $now->getTimestamp());
        if ($ms <= 0) {
            return ['txt' => 'Clôturée', 'big' => '—', 'small' => 'Clôturée', 'full' => 'Clôturée', 'urgent' => false, 'closed' => true];
        }
        $d = intdiv($ms, 86400);
        $h = intdiv($ms % 86400, 3600);
        $m = str_pad((string) intdiv($ms % 3600, 60), 2, '0', STR_PAD_LEFT);
        $urgent = $ms < self::URGENT_HOURS * 3600;
        if ($d > 0) {
            return ['txt' => "$d j $h h", 'big' => "$d j", 'small' => "$h h", 'full' => "$d j $h h $m", 'urgent' => $urgent, 'closed' => false];
        }

        return ['txt' => "$h h $m", 'big' => "$h h", 'small' => "$m min", 'full' => "$h h $m min", 'urgent' => true, 'closed' => false];
    }

    /** Taille lisible : « 14 Ko », « 1,2 Mo ». */
    public static function size(int $kb): string
    {
        return $kb >= 1000 ? str_replace('.', ',', (string) round($kb / 1024, 1)).' Mo' : $kb.' Ko';
    }

    /** « 2 h 30 », « 1 h », « 45 min ». */
    public static function duration(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($h === 0) {
            return "$m min";
        }

        return $m ? "$h h ".str_pad((string) $m, 2, '0', STR_PAD_LEFT) : "$h h";
    }

    /** Typographie française : espace insécable avant : ; ? ! » et après «. */
    public static function typo(?string $s): ?string
    {
        if ($s === null) {
            return null;
        }
        $s = preg_replace('/ ([:;?!»])/u', "\u{00A0}$1", $s);

        return preg_replace('/« /u', "«\u{00A0}", $s);
    }

    /** Normalise pour la recherche : minuscules, sans accents. */
    public static function norm(string $s): string
    {
        return Str::lower(Str::ascii($s));
    }

    /** Jour relatif : « aujourd’hui », « hier », « lundi », « 26 sept. ». */
    public static function relativeDay(CarbonInterface $d): string
    {
        $days = (int) $d->copy()->startOfDay()->diffInDays(now()->startOfDay());
        return match (true) {
            $days === 0 => 'aujourd’hui',
            $days === 1 => 'hier',
            $days < 7 => $d->locale('fr')->translatedFormat('l'),
            default => self::shortDate($d, false),
        };
    }
}
