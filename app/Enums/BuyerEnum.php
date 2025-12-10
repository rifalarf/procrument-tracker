<?php

namespace App\Enums;

enum BuyerEnum: string
{
    case DIAN_SHOLIHAT = 'DIAN_SHOLIHAT';
    case TATHU_RA = 'TATHU_RA';
    case EVA_SEPSILIA_SARI = 'EVA_SEPSILIA_SARI';
    case ATO_HERYANTO = 'ATO_HERYANTO';
    case MAIL_MARZUKI = 'MAIL_MARZUKI';
    case MUTIA_VIRGIANA = 'MUTIA_VIRGIANA';
    case ADE_SUNARYA = 'ADE_SUNARYA';
    case GUGUN_GT = 'GUGUN_GT';
    case ERIK_ERDIANA = 'ERIK_ERDIANA';
    case DICKY_SETIAGRAHA = 'DICKY_SETIAGRAHA';
    case ERWIN_HERDIANA = 'ERWIN_HERDIANA';
    case AKBAR_FATURAHMAN = 'AKBAR_FATURAHMAN';
    case EGGY_BAHARUDIN = 'EGGY_BAHARUDIN';
    case HERU_WINATA_PRAJA = 'HERU_WINATA_PRAJA';
    case NAWANG_WULAN = 'NAWANG_WULAN';
    case CHOLIDA_MARANANI = 'CHOLIDA_MARANANI';

    public function label(): string
    {
        return match($this) {
            self::DIAN_SHOLIHAT => 'Dian Sholihat',
            self::TATHU_RA => 'Tathu RA',
            self::EVA_SEPSILIA_SARI => 'Eva Sepsilia Sari',
            self::ATO_HERYANTO => 'Ato Heryanto',
            self::MAIL_MARZUKI => 'Mail Marzuki',
            self::MUTIA_VIRGIANA => 'Mutia Virgiana',
            self::ADE_SUNARYA => 'Ade Sunarya',
            self::GUGUN_GT => 'Gugun GT',
            self::ERIK_ERDIANA => 'Erik Erdiana',
            self::DICKY_SETIAGRAHA => 'Dicky Setiagraha',
            self::ERWIN_HERDIANA => 'Erwin Herdiana',
            self::AKBAR_FATURAHMAN => 'Akbar Faturahman',
            self::EGGY_BAHARUDIN => 'Eggy Baharudin',
            self::HERU_WINATA_PRAJA => 'Heru Winata Praja',
            self::NAWANG_WULAN => 'Nawang Wulan',
            self::CHOLIDA_MARANANI => 'Cholida Maranani',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DIAN_SHOLIHAT => '#e8eaed',
            self::TATHU_RA => '#d4edbc',
            self::EVA_SEPSILIA_SARI => '#ffcfc9',
            self::ATO_HERYANTO => '#ffc8aa',
            self::MAIL_MARZUKI => '#ffe5a0',
            self::MUTIA_VIRGIANA => '#bfe1f6',
            self::ADE_SUNARYA => '#e8eaed',
            self::GUGUN_GT => '#e6cff2',
            self::ERIK_ERDIANA => '#3d3d3d',
            self::DICKY_SETIAGRAHA => '#b10202',
            self::ERWIN_HERDIANA => '#753800',
            self::AKBAR_FATURAHMAN => '#473822',
            self::EGGY_BAHARUDIN => '#11734b',
            self::HERU_WINATA_PRAJA => '#0a53a8',
            self::NAWANG_WULAN => '#215a6c',
            self::CHOLIDA_MARANANI => '#5a3286',
        };
    }
}
