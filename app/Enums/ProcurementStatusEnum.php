<?php

namespace App\Enums;

enum ProcurementStatusEnum: string
{
    case DUR = 'DUR';
    case RFQ = 'RFQ';
    case BID_OPEN = 'BID_OPEN';
    case EVALUASI_TEKNIS_KOMERSIAL = 'EVALUASI_TEKNIS_KOMERSIAL';
    case KONFIRMASI_SPESIFIKASI = 'KONFIRMASI_SPESIFIKASI';
    case KONFIRMASI_ANGGARAN = 'KONFIRMASI_ANGGARAN';
    case NEGOSIASI = 'NEGOSIASI';
    case AWARDING = 'AWARDING';
    case PERSETUJUAN_PEMENANG = 'PERSETUJUAN_PEMENANG';
    case APPROVAL_PO = 'APPROVAL_PO';
    case PO = 'PO';
    case SPK = 'SPK';
    case PR_DIBATALKAN = 'PR_DIBATALKAN';
    case APP_NEGO = 'APP_NEGO';
    case TTD_SPK = 'TTD_SPK';
    case AUCTION = 'AUCTION';
    case LOI_BELUM_PO = 'LOI_BELUM_PO';
    case TTD_PO = 'TTD_PO';
    case PR_DIKEMBALIKAN_KE_PPP = 'PR_DIKEMBALIKAN_KE_PPP';
    case PO_DIBATALKAN = 'PO_DIBATALKAN';
    case REBID = 'REBID';

    public function label(): string
    {
        return match($this) {
            self::DUR => 'DUR',
            self::RFQ => 'RFQ',
            self::BID_OPEN => 'Bid Open',
            self::EVALUASI_TEKNIS_KOMERSIAL => 'Evaluasi Teknis & Komersial',
            self::KONFIRMASI_SPESIFIKASI => 'Konfirmasi Spesifikasi',
            self::KONFIRMASI_ANGGARAN => 'Konfirmasi Anggaran',
            self::NEGOSIASI => 'Negosiasi',
            self::AWARDING => 'Awarding',
            self::PERSETUJUAN_PEMENANG => 'Persetujuan Pemenang',
            self::APPROVAL_PO => 'Approval PO',
            self::PO => 'PO',
            self::SPK => 'SPK',
            self::PR_DIBATALKAN => 'PR Dibatalkan',
            self::APP_NEGO => 'App. Nego',
            self::TTD_SPK => 'TTD SPK',
            self::AUCTION => 'Auction',
            self::LOI_BELUM_PO => 'LOI/Belum PO',
            self::TTD_PO => 'TTD PO',
            self::PR_DIKEMBALIKAN_KE_PPP => 'PR dikembalikan ke PPP untuk di proses di PI',
            self::PO_DIBATALKAN => 'PO dibatalkan',
            self::REBID => 'Rebid',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DUR => '#e8eaed',
            self::RFQ => '#ffcfc9',
            self::BID_OPEN => '#ffc8aa',
            self::EVALUASI_TEKNIS_KOMERSIAL => '#ffe5a0',
            self::KONFIRMASI_SPESIFIKASI => '#d4edbc',
            self::KONFIRMASI_ANGGARAN => '#bfe1f6',
            self::NEGOSIASI => '#c6dbe1',
            self::AWARDING => '#e6cff2',
            self::PERSETUJUAN_PEMENANG => '#3d3d3d',
            self::APPROVAL_PO => '#b10202',
            self::PO => '#753800',
            self::SPK => '#e8eaed',
            self::PR_DIBATALKAN => '#b10202',
            self::APP_NEGO => '#e6cff2',
            self::TTD_SPK => '#bfe1f6',
            self::AUCTION => '#5a3286',
            self::LOI_BELUM_PO => '#0a53a8',
            self::TTD_PO => '#5a3286',
            self::PR_DIKEMBALIKAN_KE_PPP => '#e8eaed',
            self::PO_DIBATALKAN => '#b10202',
            self::REBID => '#753800',
        };
    }
}
