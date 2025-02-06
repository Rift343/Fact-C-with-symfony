<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use  Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FactController extends AbstractController
{

    #[Route(path:"/", name:"homePage")]
    public function homePage(): Response{
        return $this->render(view: "/base.html.twig",parameters: ['resultCombi' => 0,'resultFacto'=> 0,'nValueCombi'=>0,'pValueCombi'=>0,'nValueFact'=>0]);
    }

    #[Route(path:'/mastermind', name:'app_mastermind')]
    public function app_mastermind(Request $request): Response{
        $session = $request->getSession();
        #$id = uniqid("ABC",true);
        $mastermind =new MastermindService(4);
        $session->set("jeu",$mastermind);
        return $this->render(view:'/fact/mastermind.html.twig',parameters: ["jeu"=>$mastermind]);
    }

    #[Route(path:"/mastermind/sendTry", name:"sendTry")]
    public function sendTry(Request $request): Response{
        $session = $request->getSession();
        $mastermind = $session->get("jeu");
       # if ($mastermind == -1) {
        #    return $this->render(view: "/base.html.twig",parameters: ['resultCombi' => 0,'resultFacto'=> 0,'nValueCombi'=>0,'pValueCombi'=>0,'nValueFact'=>0]);
        #}
        #else{
            $code = $request->query->get('code');
            $mastermind->test($code);
            //dd($mastermind);
            if ($mastermind->isFini()) {
                return $this->render(view:'/fact/mastermind.html.twig',parameters: ["jeu"=>$mastermind]);
            }
            else{
                return $this->render(view:'/fact/mastermind.html.twig',parameters: ["jeu"=>$mastermind]);
                
            }
        #}

    }



    #[Route('/fact', name: 'app_fact')]
    public function index(): Response
    {
        return $this->render('fact/index.html.twig', [
            'controller_name' => 'FactController',
        ]);
    }

    #[Route(path: '/facto', name:'app_facto',methods:['GET'])]
    public function app_facto(Request $request): Response{
        $param = $request->query->all();
        $n = $param['n'];
        $res = calcul::factorielle($n);
        return  $this->render('/base.html.twig', ['resultCombi' => 0,'resultFacto'=> $res,'nValueCombi'=>0,'pValueCombi'=>0,'nValueFact'=>$n]);    }

    #[Route('/combi', name:'app_combi',methods:['GET'])]
    public function app_combi(Request $request): Response{
        $param = $request->query->all();
        $n =$param['n'];
        $p = $param['p'];
        $res =calcul::combinaison($n, $p);
        return  $this->render('/base.html.twig', ['resultCombi' => $res,'resultFacto'=>0,'nValueCombi'=>$n,'pValueCombi'=>$p,'nValueFact'=>0]);
    }
}


final class calcul 
{
    public static function factorielle(int $n): float{
        if ($n <= 0) {
            return 0;
        }
        else{
            $response = 1;
            for ($i=2; $i <= $n ; $i++) { 
                $response = $response * $i;
            }
            return $response;
        }
    }

    public static function combinaison(int $n, int $p):float{
        $factN = calcul::factorielle($n);//n!
        $factP = calcul::factorielle($p);//p!
        $factNmp = calcul::factorielle($n - $p);//(n-p)!
        if ($factNmp <= 0 or $factP <= 0 or $factN <= 0 ) {
            return 0;
        }
        return $factN /($factP * $factNmp);//n!/(p!*(n-p)!)
    }
}


class MastermindService{
    private $taille;
    private $number;
    private $arrayNumber;
    private $win = false;
    private $essais = [];

    public function __construct(int $taille){
        $this->taille = $taille;
        $temp = 1;
        for ($i=0; $i < $taille; $i++) { 
            $temp = $temp * 10;
        }
        $this->number =random_int($temp, ($temp*10)-1);
        $this->arrayNumber = array_map('intval', str_split($this->number));
    }

    public function test($code){

        $arrayCode  = array_map('intval', str_split($code));
        
        if ($code == $this->number) {
            $this->win = true;
            array_push($this->essais, [$code, $this->taille]);
            return true;
        } else {
            $nbJuste = 0;
            for ($i=0; $i < count($arrayCode); $i++) { 
                if($arrayCode[$i] == $this->arrayNumber[$i]){
                    $nbJuste++;
                }
            }
            array_push($this->essais, [$code, $nbJuste]);
            return false;
        }        
    }

    public function getEssais(){
        return $this->essais;
    }

    public function getTaille(){
        return $this->taille;
    }

    public function isFini(){
        return $this->win;
    }
}