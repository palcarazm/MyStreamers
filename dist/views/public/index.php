<?php

use Model\Sitio;
?>

<div class="row">
    <main class="col-lg-7 col-xl-8">
        <div class="card mb-3">
            <h5 class="card-header text-white bg-primary">Sobre <?php Sitio::printTitulo() ?></h5>
            <div class="card-body">
                <?php Sitio::printDescripcion() ?>
            </div>
        </div><!-- Sobre la serie -->
        <div class="card mb-3" id="participantes">
            <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center">
                <h5 class="mb-0 flex-grow-1">En directo <span class="badge badge-pill badge-light" id="badge-directo"></span></h5>
                <a class="btn btn-outline-light btn-sm" href="/participantes">Ver más</a>
            </div>
            <div class="card-body">
                <div class="online row">En estos momentos no hay nadie en directo</div>
                <div class="offline" style="display:none;">
                    <?php
                    foreach ($participantes as $participante) {
                        $participante->printCard('TWITCH');
                    }
                    ?>
                </div>
            </div>
        </div><!-- En directo -->
        <?php if (!Sitio::isEnabled('eventos') && !Sitio::isEnabled('noticias')) : ?>
    </main>
    <aside class="col-lg-5 col-xl-4">
    <?php endif; ?>
    <div class="card mb-3">
        <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center">
            <h5 class="mb-0 flex-grow-1">Últimos videos</h5>
            <a class="btn btn-outline-light btn-sm" href="/videos">Ver más</a>
        </div>
        <div class="card-body">
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum distinctio, eligendi unde sit facere beatae veritatis exercitationem quis omnis repudiandae fuga ducimus illo odio mollitia quia. Reiciendis tempore provident est non nam quisquam eligendi, ipsum nostrum deserunt maiores inventore impedit hic, tenetur exercitationem perferendis molestias, numquam ex ullam corrupti qui placeat? Deleniti, officia asperiores autem repudiandae tenetur explicabo nisi reprehenderit voluptate? Voluptas id sed facere quis amet. Autem, fugiat. Sequi mollitia, hic aspernatur asperiores repellat impedit ea quidem sunt architecto omnis doloribus ad, fugit nemo iure veritatis adipisci consequuntur excepturi cum in quos voluptatum earum necessitatibus beatae voluptates. Sunt, similique!
        </div>
    </div><!-- Videos -->
    <?php if (Sitio::isEnabled('eventos') || Sitio::isEnabled('noticias')) : ?>
        </main>
        <aside class="col-lg-5 col-xl-4">
        <?php endif; ?>
        <?php if (Sitio::isEnabled('eventos')) : ?>
            <div class="card mb-3">
                <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 flex-grow-1">Eventos</h5>
                    <a class="btn btn-outline-light btn-sm" href="/eventos">Ver más</a>
                </div>
                <div class="card-body">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum distinctio, eligendi unde sit facere beatae veritatis exercitationem quis omnis repudiandae fuga ducimus illo odio mollitia quia. Reiciendis tempore provident est non nam quisquam eligendi, ipsum nostrum deserunt maiores inventore impedit hic, tenetur exercitationem perferendis molestias, numquam ex ullam corrupti qui placeat? Deleniti, officia asperiores autem repudiandae tenetur explicabo nisi reprehenderit voluptate? Voluptas id sed facere quis amet. Autem, fugiat. Sequi mollitia, hic aspernatur asperiores repellat impedit ea quidem sunt architecto omnis doloribus ad, fugit nemo iure veritatis adipisci consequuntur excepturi cum in quos voluptatum earum necessitatibus beatae voluptates. Sunt, similique!
                </div>
            </div><!-- Eventos -->
        <?php endif;
        if (Sitio::isEnabled('noticias')) : ?>
            <div class="card mb-3">
                <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 flex-grow-1">Noticias</h5>
                    <a class="btn btn-outline-light btn-sm" href="/noticias">Ver más</a>
                </div>
                <div class="card-body">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum distinctio, eligendi unde sit facere beatae veritatis exercitationem quis omnis repudiandae fuga ducimus illo odio mollitia quia. Reiciendis tempore provident est non nam quisquam eligendi, ipsum nostrum deserunt maiores inventore impedit hic, tenetur exercitationem perferendis molestias, numquam ex ullam corrupti qui placeat? Deleniti, officia asperiores autem repudiandae tenetur explicabo nisi reprehenderit voluptate? Voluptas id sed facere quis amet. Autem, fugiat. Sequi mollitia, hic aspernatur asperiores repellat impedit ea quidem sunt architecto omnis doloribus ad, fugit nemo iure veritatis adipisci consequuntur excepturi cum in quos voluptatum earum necessitatibus beatae voluptates. Sunt, similique!
                </div>
            </div><!-- Noticias -->
        <?php endif; ?>
        </aside>
</div>