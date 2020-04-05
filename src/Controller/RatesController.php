<?php

namespace App\Controller;

use App\Repository\ExchangeRateRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RatesController extends AbstractController
{
    const DATE__LIMIT = 100;
    const PAGE__LIMIT = 20;
    const LAST_DATES_LIMIT = 5;

    private ExchangeRateRepository $repo;
    private PaginatorInterface $paginator;

    public function __construct(ExchangeRateRepository $repo, PaginatorInterface $paginator)
    {
        $this->repo = $repo;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/all-dates", name="all-dates")
     *
     * @return Response
     */
    public function allDates()
    {
        return $this->render('rates/all-dates.html.twig', [
            'dates' => $this->repo->findDates(static::DATE__LIMIT),
            'date_limit' => static::DATE__LIMIT
        ]);
    }

    /**
     * @Route("/currency/{currency}", name="currency")
     *
     * @param string $currency
     *
     * @return Response|NotFoundHttpException
     */
    public function currency(string $currency)
    {
        $rates = $this->repo->findForCurrency($currency);

        if (!$rates) {
            return $this->createNotFoundException();
        }

        return $this->render('rates/rates-for-currency.html.twig', [
            'rates' => $rates,
            'currency' => $currency,
        ]);
    }


    /**
     * @Route("/date/{date}", name="rates")
     *
     * @param Request $request
     * @param string $date
     *
     * @return Response|NotFoundHttpException
     */
    public function index(Request $request, string $date = '')
    {
        $lastDate = $this->repo->findLast();

        if (!$lastDate) {
            return $this->render('rates/no-rates.html.twig');
        }

        if ($date === '') {
            $dt = $lastDate ? $lastDate->getDate() : new \DateTime();
        } else {
            try {
                $dt = new \DateTime($date);
            } catch (\Throwable $e) {
                return $this->createNotFoundException();
            }
        }

        $queryBuilder = $this->repo->getRatesQueryBuilder($dt);
        $page = $request->query->getInt('page', 1);
        $pagination = $this->paginator->paginate($queryBuilder, $page, static::PAGE__LIMIT);

        if (!$pagination->getTotalItemCount()) {

            return $this->createNotFoundException();
        }

        return $this->render('rates/rates-for-day.html.twig', [
            'today' => $dt,
            'pagination' => $pagination,
            'limit' => static::PAGE__LIMIT > $pagination->getTotalItemCount() ? static::PAGE__LIMIT : $pagination->getTotalItemCount(),
            'dates' => $this->repo->findDates(static::LAST_DATES_LIMIT),
        ]);
    }
}
