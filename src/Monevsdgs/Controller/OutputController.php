<?php

namespace Monevsdgs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class OutputController extends AbstractActionController {

    private $model;

    public function onDispatch(MvcEvent $e) {
        $sl = $this->getServiceLocator();
        $this->model = new \stdClass();
        // list loaded model
        $this->model->tujuan = $sl->get('Master\Model\SdgstujuanTable');
        $this->model->global = $sl->get('Master\Model\SdgsglobalTable');
        $this->model->nasional = $sl->get('Master\Model\SdgsnasionalTable');
        $this->model->provinsi = $sl->get('Master\Model\SdgsprovinsiTable');
        $this->model->provinsii = $sl->get('Master\Model\SdgsprovinsiindikatorTable');
        $this->model->kotai = $sl->get('Master\Model\SdgskotaindikatorTable');
        $this->model->coding = $sl->get('Monevsdgs\Model\SdgscodingTable');

        return parent::onDispatch($e);
    }
    public function indexAction() {
        $this->layout()->setVariable("title_page", 'Coding SDGS');
        $this->layout()->setVariable("active_page", '');
        $this->layout()->setVariable("breadcrumbs", array(
                'SDGS' => '#', // No link
                'Coding' => '', // Link
                ));
        $sql = "SELECT SDGS_IndikatorKota.*, SDGS_IndikatorProvinsi.nama_indikator_provinsi, SDGS_Global.target_global, SDGS_nasional.target_nasional, SDGS_Tujuan.nama_tujuan
                FROM SDGS_IndikatorKota 
                JOIN SDGS_IndikatorProvinsi ON SDGS_IndikatorProvinsi.id_indikator_provinsi=SDGS_IndikatorKota.id_indikator_provinsi 
                -- JOIN SDGS_Provinsi ON SDGS_Provinsi.id_target_provinsi=SDGS_IndikatorProvinsi.id_target_provinsi 
                JOIN SDGS_Nasional ON SDGS_Nasional.id_target_nasional=SDGS_IndikatorProvinsi.id_target_nasional 
                JOIN SDGS_Global ON SDGS_Global.id_target_global=SDGS_Nasional.id_target_global 
                JOIN SDGS_Tujuan ON SDGS_Tujuan.id_sdgs=SDGS_Global.id_sdgs 
                WHERE SDGS_IndikatorKota.isDeleted=0";
        $kotai = $this->model->kotai->query($sql);
        return new ViewModel(array(
            'flash_message' => $this->flashMessenger()->getMessages(),
            'params' => $this->params()->fromQuery(),
            'data'   => $kotai
        ));
    }
    public function kegiatanAction() {
        $session = new Container('user_data');
         $this->layout()->setVariable("title_page", 'Coding SDGS');
        $this->layout()->setVariable("active_page", '');
        $this->layout()->setVariable("breadcrumbs", array(
                'SDGS' => '#', // No link
                'Coding' => '', // Link
                'Kegiatan' => '' // Active Page
            ));
        if($session->id_role == 4){
            $WHERE = 'and SKPD.id_skpd = '.$session->id_skpd;
        }elseif($session->id_role == 6){
            $WHERE = 'and SKPD.id_bidang = '.$session->id_bidang;
        }else{
            $WHERE = '';
        }
        $sql = "SELECT  Bidang.bidang, SKPD.nama_skpd, Urusan.nama_urusan, Lima_ProgramRPJMD.id_program_rpjmd, 
                    Program.nama_program, Kegiatan.nama_kegiatan, 
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_1 > '' THEN '2018 '
                    END AS t1,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_2 > '' THEN '2019 '
                    END AS t2,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_3 > '' THEN '2020 '
                    END AS t3,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_4 > '' THEN '2021 '
                    END AS t4,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_5 > '' THEN '2022'
                    END AS t5,
                    Lima_KegiatanRenstra.lokasi,Lima_KegiatanRenstra.rp_1,Lima_KegiatanRenstra.rp_2,Lima_KegiatanRenstra.rp_3,Lima_KegiatanRenstra.rp_4,Lima_KegiatanRenstra.rp_5,Lima_KegiatanRenstra.rp_a,Lima_KegiatanRenstra.id_kegiatan_renstra FROM Lima_SasaranRenstra
                    JOIN Lima_SasaranRPJMD ON Lima_SasaranRPJMD.id_sasaran_rpjmd = Lima_SasaranRenstra.id_sasaran_rpjmd
                    JOIN Lima_StrategiRenstra ON Lima_StrategiRenstra.id_sasaran = Lima_SasaranRenstra.id_sasaran
                    JOIN Lima_KegiatanRenstra ON Lima_KegiatanRenstra.id_kegiatan_renstra = Lima_StrategiRenstra.id_kegiatan_renstra
                    JOIN Lima_KebijakanRenstra ON Lima_KebijakanRenstra.id_kebijakan = Lima_KegiatanRenstra.id_kebijakan
                    JOIN Lima_ProgramSKPD ON Lima_ProgramSKPD.id_program_skpd = Lima_KebijakanRenstra.id_program_skpd 
                    JOIN Kegiatan ON Kegiatan.id_kegiatan = Lima_KegiatanRenstra.id_kegiatan
                    
                    JOIN Lima_ProgramRPJMD ON Lima_ProgramRPJMD.id_program_rpjmd = Lima_ProgramSKPD.id_program_rpjmd
                    JOIN Program ON Program.id_program = Lima_ProgramRPJMD.id_program AND Program.id_program NOT IN (943,944,945,946,947,948)
                    JOIN Lima_Renstra ON Lima_Renstra.id_renstra = Lima_SasaranRenstra.id_renstra
                    JOIN SKPD ON SKPD.id_skpd = Lima_Renstra.id_skpd
                    JOIN Urusan ON Urusan.id_urusan = Program.id_urusan
                    JOIN Bidang ON Bidang.id_bidang = SKPD.id_bidang
                    WHERE Lima_Renstra.isDeleted = 0 ".$WHERE."
                    GROUP BY Bidang.bidang, SKPD.nama_skpd, Urusan.nama_urusan, Lima_ProgramRPJMD.id_program_rpjmd,
                Lima_ProgramRPJMD.rp_1 ,
                Lima_ProgramRPJMD.rp_2 ,
                Lima_ProgramRPJMD.rp_3 ,
                Lima_ProgramRPJMD.rp_4 ,
                Lima_ProgramRPJMD.rp_5 ,
                Lima_ProgramRPJMD.rp_a , 
                    Program.nama_program, Kegiatan.nama_kegiatan, 
                    Lima_KegiatanRenstra.lokasi,Lima_KegiatanRenstra.rp_1,Lima_KegiatanRenstra.rp_2,Lima_KegiatanRenstra.rp_3,Lima_KegiatanRenstra.rp_4,Lima_KegiatanRenstra.rp_5,Lima_KegiatanRenstra.rp_a,Lima_KegiatanRenstra.id_kegiatan_renstra
                    ORDER BY nama_program,nama_kegiatan";
        $data = $this->model->tujuan->query($sql);
        $sql2 = "SELECT SDGS_IndikatorKota.*, SDGS_IndikatorProvinsi.nama_indikator_provinsi, SDGS_Global.target_global, SDGS_nasional.target_nasional, SDGS_Tujuan.nama_tujuan
                FROM SDGS_IndikatorKota 
                JOIN SDGS_IndikatorProvinsi ON SDGS_IndikatorProvinsi.id_indikator_provinsi=SDGS_IndikatorKota.id_indikator_provinsi 
                -- JOIN SDGS_Provinsi ON SDGS_Provinsi.id_target_provinsi=SDGS_IndikatorProvinsi.id_target_provinsi 
                JOIN SDGS_Nasional ON SDGS_Nasional.id_target_nasional=SDGS_IndikatorProvinsi.id_target_nasional 
                JOIN SDGS_Global ON SDGS_Global.id_target_global=SDGS_Nasional.id_target_global 
                JOIN SDGS_Tujuan ON SDGS_Tujuan.id_sdgs=SDGS_Global.id_sdgs 
                WHERE SDGS_IndikatorKota.id_indikator_kota=".$_REQUEST['id_indikator_kota'];
        $tujuan = $this->model->kotai->query($sql2);
        $sql3 = "SELECT SDGS_Coding.id_coding, SDGS_IndikatorKota.*, Program.nama_program, Kegiatan.nama_kegiatan,
                CASE 
                        WHEN Lima_KegiatanRenstra.rp_1 > '' THEN '2018 '
                    END AS t_1,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_2 > '' THEN '2019 '
                    END AS t_2,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_3 > '' THEN '2020 '
                    END AS t_3,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_4 > '' THEN '2021 '
                    END AS t_4,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_5 > '' THEN '2022'
                    END AS t_5,
                    Lima_KegiatanRenstra.lokasi,Lima_KegiatanRenstra.rp_1,Lima_KegiatanRenstra.rp_2,Lima_KegiatanRenstra.rp_3,Lima_KegiatanRenstra.rp_4,Lima_KegiatanRenstra.rp_5
                FROM SDGS_Coding
                JOIN SDGS_IndikatorKota ON SDGS_IndikatorKota.id_indikator_kota=SDGS_Coding.id_indikator_kota
                JOIN Lima_KegiatanRenstra ON Lima_KegiatanRenstra.id_kegiatan_renstra = SDGS_Coding.id_kegiatan_renstra
                JOIN Lima_KebijakanRenstra ON Lima_KebijakanRenstra.id_kebijakan = Lima_KegiatanRenstra.id_kebijakan
                JOIN Lima_ProgramSKPD ON Lima_ProgramSKPD.id_program_skpd = Lima_KebijakanRenstra.id_program_skpd 
                JOIN Kegiatan ON Kegiatan.id_kegiatan = Lima_KegiatanRenstra.id_kegiatan
                JOIN Lima_ProgramRPJMD ON Lima_ProgramRPJMD.id_program_rpjmd = Lima_ProgramSKPD.id_program_rpjmd
                JOIN Program ON Program.id_program = Lima_ProgramRPJMD.id_program AND Program.id_program NOT IN (943,944,945,946,947,948)
                WHERE SDGS_Coding.isDeleted=0 and SDGS_Coding.id_indikator_kota=".$_REQUEST['id_indikator_kota'];
                // WHERE 
                // ORDER BY SDGS_Coding.id_coding DESC";
                // $this->model->coding->order_by('SDGS_Coding.id_coding', 'DESC');
        $coding = $this->model->coding->query($sql3);
        //var_dump($sql3);
        //die;
        return new ViewModel(array(
            'flash_message' => $this->flashMessenger()->getMessages(),
            'params' => $this->params()->fromQuery(),
            'data' => $data,
            'tujuan' => $tujuan[0],
            'coding' => $coding,
        ));
    }

    public function laporanAction() {
        $session = new Container('user_data');
         $this->layout()->setVariable("title_page", 'Coding SDGS');
        $this->layout()->setVariable("active_page", '');
        $this->layout()->setVariable("breadcrumbs", array(
                'SDGS' => '#', // No link
                'Coding' => '', // Link
                'Laporan' => '' // Active Page
            ));
        $sql2 = "SELECT SDGS_IndikatorKota.*, SDGS_IndikatorProvinsi.nama_indikator_provinsi, SDGS_nasional.target_nasional, SDGS_Global.target_global, SDGS_Tujuan.nama_tujuan
                FROM SDGS_IndikatorKota 
                JOIN SDGS_IndikatorProvinsi ON SDGS_IndikatorProvinsi.id_indikator_provinsi=SDGS_IndikatorKota.id_indikator_provinsi 
                -- JOIN SDGS_Provinsi ON SDGS_Provinsi.id_target_provinsi=SDGS_IndikatorProvinsi.id_target_provinsi 
                JOIN SDGS_Nasional ON SDGS_Nasional.id_target_nasional=SDGS_IndikatorProvinsi.id_target_nasional 
                JOIN SDGS_Global ON SDGS_Global.id_target_global=SDGS_Nasional.id_target_global 
                JOIN SDGS_Tujuan ON SDGS_Tujuan.id_sdgs=SDGS_Global.id_sdgs";
        $tujuan = $this->model->kotai->query($sql2);
        $sql3 = "SELECT SDGS_Coding.id_coding, SDGS_IndikatorKota.*, SDGS_IndikatorProvinsi.nama_indikator_provinsi, SDGS_nasional.target_nasional, SDGS_Global.target_global, SDGS_Tujuan.nama_tujuan, Program.nama_program, Kegiatan.nama_kegiatan,
                CASE 
                        WHEN Lima_KegiatanRenstra.rp_1 > '' THEN '2018 '
                    END AS t_1,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_2 > '' THEN '2019 '
                    END AS t_2,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_3 > '' THEN '2020 '
                    END AS t_3,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_4 > '' THEN '2021 '
                    END AS t_4,
                    CASE 
                        WHEN Lima_KegiatanRenstra.rp_5 > '' THEN '2022'
                    END AS t_5,
                    Lima_KegiatanRenstra.lokasi,Lima_KegiatanRenstra.rp_1,Lima_KegiatanRenstra.rp_2,Lima_KegiatanRenstra.rp_3,Lima_KegiatanRenstra.rp_4,Lima_KegiatanRenstra.rp_5
                FROM SDGS_Coding
                JOIN SDGS_IndikatorKota ON SDGS_IndikatorKota.id_indikator_kota=SDGS_Coding.id_indikator_kota
                JOIN SDGS_IndikatorProvinsi ON SDGS_IndikatorProvinsi.id_indikator_provinsi=SDGS_IndikatorKota.id_indikator_provinsi 
                JOIN SDGS_Nasional ON SDGS_Nasional.id_target_nasional=SDGS_IndikatorProvinsi.id_target_nasional 
                JOIN SDGS_Global ON SDGS_Global.id_target_global=SDGS_Nasional.id_target_global 
                JOIN SDGS_Tujuan ON SDGS_Tujuan.id_sdgs=SDGS_Global.id_sdgs
                JOIN Lima_KegiatanRenstra ON Lima_KegiatanRenstra.id_kegiatan_renstra = SDGS_Coding.id_kegiatan_renstra
                JOIN Lima_KebijakanRenstra ON Lima_KebijakanRenstra.id_kebijakan = Lima_KegiatanRenstra.id_kebijakan
                JOIN Lima_ProgramSKPD ON Lima_ProgramSKPD.id_program_skpd = Lima_KebijakanRenstra.id_program_skpd 
                JOIN Kegiatan ON Kegiatan.id_kegiatan = Lima_KegiatanRenstra.id_kegiatan
                JOIN Lima_ProgramRPJMD ON Lima_ProgramRPJMD.id_program_rpjmd = Lima_ProgramSKPD.id_program_rpjmd
                JOIN Program ON Program.id_program = Lima_ProgramRPJMD.id_program AND Program.id_program NOT IN (943,944,945,946,947,948)
                WHERE SDGS_Coding.isDeleted = 0";
        $data = $this->model->coding->query($sql3);
        // var_dump($data);
        // die;
        return new ViewModel(array(
            'flash_message' => $this->flashMessenger()->getMessages(),
            'params' => $this->params()->fromQuery(),
            'tujuan' => $tujuan[0],
            'data' => $data,
        ));
    }

    public function codingAction() {
            // var_dump($_REQUEST);
            // die;
            $id_indikator_kota = $_REQUEST['id_indikator_kota'];
            $id_kegiatan_renstra = $_REQUEST['id_kegiatan_renstra'];
            $data = json_decode($id_kegiatan_renstra, true);
            foreach($data as $row){
                $a = array(
                    'id_indikator_kota' => $id_indikator_kota,
                    'id_kegiatan_renstra' => $row,
                    'isDeleted' => 0,
                );  
                if ($this->model->coding->insert($a)){
                    // $this->flashMessenger()->addMessage('Data berhasil disimpan');
                }else{
                    // $this->flashMessenger()->addMessage('Data gagal disimpan');
                }
            }
        // $tanya = '?';
        //$paramName = $this->getEvent()->getRouteMatch()->getParam('id_indikator_kota');
        // var_dump($this->params()->fromPost('id_indikator_kota'));
        // die;
        return $this->redirect()->toRoute('coding', array(
                            
                    // 'action' => 'kegiatan'.$tanya.'id_indikator_kota='.$id_indikator_kota,
                    'action' => 'kegiatan',
                    'param1' => 'id_indikator_kota',
                    'param2' => $this->params()->fromPost('id_indikator_kota'),
                    // var_dump($this->params()->fromRoute('id_indikator_kota')),
        ));
    }  

    public function delAction() {
        $data = array(
            'isDeleted' => 1,
            'id_coding' => $_REQUEST['id_coding'],
            'id_indikator_kota' => $_REQUEST['id_indikator_kota'],
        );
        $this->model->coding->update($data);
        // $this->flashMessenger()->addMessage('Data Berhasil dihapus');
        return $this->redirect()->toRoute('coding', array(
                    // 'action' => 'index',
                    'action' => 'kegiatan',
                    'param1' => 'id_indikator_kota',
                    'param2' => $data['id_indikator_kota'],
        ));
    }

}
