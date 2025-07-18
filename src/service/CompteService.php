<?php
namespace Src\Service;
use Src\Repository\CompteRepository;
use App\Core\App;

class CompteService{
    private CompteService $compteService;
    private CompteRepository $compteRepository;
    

    private static ?CompteService $instance = null;

    public static function getInstance(): CompteService{
        if(self::$instance == null){
            self::$instance = new CompteService();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->compteRepository = App::getDependency('repository', 'compteRepo');
    }

    public function getSoldePrincipalByUserId(int $userId): float
    {
        return $this->compteRepository->getSoldeByUserId($userId);
    }

    

}