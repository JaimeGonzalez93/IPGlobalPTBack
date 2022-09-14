<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Pelicula;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\PeliculaRepository;


class PeliculaController extends AbstractController
{

    private $client;
    
    public function __construct(HttpClientInterface $client)
    {
        $this -> client = $client;
    }

    //Obtener Lista de Peliculas
    
    /**
     * @Route("/peliculas", name="obtenerPeliculas", methods= {"GET"})
     */
    public function getPeliculas():JsonResponse
    {
        $response = $this -> client -> request('GET','https://api.themoviedb.org/3/search/movie?api_key=8f781d70654b5a6f2fa69770d1d115a3&query=harry+potter');
        $respuesta = $response ->getContent();
        $peliculas = json_decode($respuesta);
        return $this->json($peliculas);
    }

    //Obtener detalle de peliculas

    //Almacenar peliculas
    /**
     * @Route("/AlmacenarPelicula/{titulo}/{poster}/{fechaEstreno}/{valoracionApi}"), name="Almacenar_pelicula", methods={"POST"})
     */
    public function almacenarPeliculas (ManagerRegistry $doctrine, int $id, $titulo, $poster, $fechaEstreno, $valoracion)
    {
        $pelicula = $doctrine -> getRepository(Pelicula::class) -> find($id);

        if($pelicula)
        {
            return "Ya existe la pelicula";
        }

        $newPelicula = new Pelicula();
        $newPelicula -> setTitulo($titulo);
        $newPelicula -> setPoster ($poster);
        $newPelicula -> setFechaEstreno($fechaEstreno);
        $newPelicula -> setValoracion($valoracion);
    }

    /**
     * @Route("/pelicula/{id}", name="pelicula_show", methods= {"GET"})
     */

    public function peliculaDetalle(ManagerRegistry $doctrine, int $id): Response
    {
        $pelicula = $doctrine -> getRepository(Pelicula::class) -> find($id);

        if(!$pelicula){
            return $this -> json('Pelicula no encontrada '. 404);
        }

        $data = [
            'id' => $pelicula -> getId(),
            'titulo' => $pelicula -> getTitulo(),
            'poster' => $pelicula -> getPoster(),
            'fechaEstreno' => $pelicula -> getFechaEstreno(),
            'valoracion' => $pelicula -> getValoracion()
        ];
        return $this->json($data);
    }

    //Enviar valoración a api TMDB

    /**
     * @Route("/pelicula/{id}/{valoracion}", name="pelicula_valoracion", methods= {"POST"})
     */

    public function valoracion (int $id, int $valoracion)
    {
        $codigoInvitado = "ea9bc72e06943280f1f8ea5883ac636e";
        

        $response = $this-> client -> request ('POST', "https://api.themoviedb.org/3/movie/".$id."/rating?api_key=8f781d70654b5a6f2fa69770d1d115a3&guest_session_id=".$codigoInvitado,
        [
            'headers' => [ 'Content-Type' => 'application/json;charset=utf-8'],
            'body' => ['value' => $valoracion]
            ]
    );
        return $response;
    }

    /**
     * 
     */

    

}
