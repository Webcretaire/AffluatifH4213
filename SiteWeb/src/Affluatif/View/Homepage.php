<?php

namespace Affluatif\View;

/**
 * Class Homepage
 *
 * @package Affluatif\View
 */
class Homepage extends BaseTemplate
{
    protected function blockHead()
    {
        parent::blockHead();
        ?>
        <link rel="stylesheet" href="/css/pricing.css"/>
        <?php
    }

    protected function blockBanner()
    {
        ?>
        <h2 class="text-white black-glow">Affluatif</h2>
        <p class="text-white black-glow">Analyse de flux vidéo et identification de points d'affluence</p>
        <?php
    }

    protected function blockSections()
    {
        ?>
        <section class="about-area section-gap">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 about-left">
                        <h6>Affluatif</h6>
                        <h1>Tirez plus de vos caméras de surveillance</h1>
                        <p>
                            <span>
                                Analyse vidéo, historique d'affluence et prévision
                            </span>
                        </p>
                        <p>
                            Nous utilisons vos images pour détecter l'affluence à l'aide des meilleurs réseaux de
                            neurones existants, et réalisons des prédictions fiables pour les prochains jours.
                        </p>
                        <?php
                        if ($this->services->getSecurite()->isConnecte()) {
                            ?>
                            <a class="primary-btn" href="/p/deconnexion" style="color:#eee">
                                Déconnexion
                            </a>
                            <?php
                        } else {
                            ?>
                            <a class="primary-btn"
                               onclick="loadModale('/m/connexion', 'modale__connexion');"
                               style="cursor:pointer; color:#eee">
                                Connexion
                            </a>
                            <?php
                        }
                        ?>
                        <a class="primary-btn"
                           onclick="loadModale('/m/contact', 'modale__contact');"
                           style="cursor:pointer; color:#eee">
                            Plus d'informations
                        </a>
                    </div>
                    <div class="col-lg-6 about-right">
                        <div class="active-about-carusel">
                            <div class="single-carusel item">
                                <img class="img-fluid" src="/img/rcnn1.jpg" alt="">
                            </div>
                            <div class="single-carusel item">
                                <img class="img-fluid" src="/img/rcnn2.jpg" alt="">
                            </div>
                            <div class="single-carusel item">
                                <img class="img-fluid" src="/img/graph.jpg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing_section" class="about-generic-area pb-50">
            <h1>Nos tarifs</h1>

            <div class="price-table-wrapper">
                <div class="pricing-table">
                    <h2 class="pricing-table__header">- BASIC -</h2>
                    <h3 class="pricing-table__price">
                        <del style="color:#ff6666">
                            <small>50€</small>
                        </del>
                        <br/>45€
                    </h3>
                    <a style="cursor: pointer" class="pricing-table__button"
                       onclick="loadModale('/m/contact', 'modale__contact')">
                        Plus d'informations
                    </a>
                    <ul class="pricing-table__list">
                        <li>0 jours d'essai gratuit</li>
                        <li>42 bits de stockage</li>
                        <li>-100% de réduction</li>
                        <li>Support gratuit</li>
                    </ul>
                </div>
                <div class="pricing-table featured-table">
                    <h2 class="pricing-table__header">- PRO -</h2>
                    <h3 class="pricing-table__price">
                        <del style="color:#ff6666">
                            <small>100€</small>
                        </del>
                        <br/>80€
                    </h3>
                    <a style="cursor: pointer" class="pricing-table__button"
                       onclick="loadModale('/m/contact', 'modale__contact')">
                        Plus d'informations
                    </a>
                    <ul class="pricing-table__list">
                        <li>1 jour d'essai gratuit</li>
                        <li>5 disquettes de stockage</li>
                        <li>0% de réduction</li>
                        <li>emojis sur les vidéos</li>
                    </ul>
                </div>
                <div class="pricing-table">
                    <h2 class="pricing-table__header">- ENTERPRISE -</h2>
                    <h3 class="pricing-table__price">
                        <del style="color:#ff6666">
                            <small>200€</small>
                        </del>
                        <br/>120€
                    </h3>
                    <a style="cursor: pointer" class="pricing-table__button"
                       onclick="loadModale('/m/contact', 'modale__contact')">
                        Plus d'informations
                    </a>
                    <ul class="pricing-table__list">
                        <li>5 jours d'essai gratuit</li>
                        <li>500GB de stockage</li>
                        <li>50% de réduction</li>
                        <li>emojis + tags putaclik</li>
                    </ul>
                </div>
            </div>
            <!-- End Generic Start -->
        </section>
        <?php
    }
}