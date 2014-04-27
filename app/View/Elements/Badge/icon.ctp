<a href="<? echo $this->Html->url('/badges/view/' . $badge['Badge']['id']) ?>" 
    title="<? echo h($badge['Badge']['name']); ?>"
    style="background-color: <? echo $badge['Domain']['color']?>; border-color: <? echo $badge['Domain']['color']?>" 
    class="btn btn-primary btn-large">
    <i class="<? echo $badge['Badge']['icon']?>"></i>
</a>