<div class="tile-title" style="background-color: <? echo $badge['Domain']['color']?>">           
    <div class="icon">
        <a href="<? echo $this->Html->url('/badges/view/' . $badge['Badge']['id']) ?>"><i class="<? echo $badge['Badge']['icon']?>"></i></a>
    </div>
    <div class="title">
        <a href="<? echo $this->Html->url('/badges/view/' . $badge['Badge']['id']) ?>">
            <h3><? echo h($badge['Badge']['name']); ?></h3>
            <br/>
        </a>
    </div>
</div>