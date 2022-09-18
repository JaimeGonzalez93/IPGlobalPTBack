<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Pelicula;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PeliculaController
 */

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
    public function getPeliculas(EntityManagerInterface $doctrine):JsonResponse
    {
        $datosApi = $this -> client -> request('GET','https://api.themoviedb.org/3/search/movie?api_key=8f781d70654b5a6f2fa69770d1d115a3&query=harry+potter');
        
        $repositorio = $doctrine -> getRepository(pelicula::class);
        $peliculas = $repositorio -> findAll();

        $data=[];
    
        if($peliculas) {
                foreach ($peliculas as $pelicula)
                {
                    $data[] = [
                            'id' => $pelicula->getId(),
                            'titulo' => $pelicula ->getTitulo(),
                            'fechaEstreno' => $pelicula->getFechaEstreno(),
                            'poster' => $pelicula -> getPoster(),
                            'valoracion' => $pelicula -> getValoracion(),
                    ];
                };
            return $this -> json($data);
            
        };

        $contenidoApi = $datosApi->getContent();
        $data = json_decode($contenidoApi, true);

        var_dump ($data["results"]);

        foreach($data["results"] as $pelicula)
        {
            $new = new Pelicula;
            $new 
                ->setTitulo($pelicula['title'])
                ->setPoster($pelicula['poster_path'])
                ->setFechaEstreno($pelicula['release_date'])
                ->setValoracion($pelicula['vote_average']);
            
                $doctrine-> persist($new);
                $doctrine-> flush();
        };
        
    }

    //Obtener detalle de peliculas

    /**
     * @Route("/pelicula/{id}", name="pelicula_show", methods= {"GET"})
     */

    public function peliculaDetalle(ManagerRegistry $doctrine, int $id): JsonResponse
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
     * @Route("/valoracion/{id}/{valoracion}", name="pelicula_valoracion", methods= {"POST"})
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
}