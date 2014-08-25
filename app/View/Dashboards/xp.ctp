<style type="text/css">
    .xp-dashboard td {
        text-align: center;
        padding: 0px !important;
    }
    .xp {
        width: 100%;
    }
</style>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Daily XP Dashboard</h1>
        </div>
    </div>
    <div class="panel-body">
        <table id="xp-table" class="table table-condensed xp-dashboard">
            <tbody>
                <? foreach ($logs as $day => $entries): ?>
                    <tr>
                        <td style="padding: 3px !important"><strong><?= $this->Format->date($day, 'M jS') ?></strong></td>
                        <? foreach ($entries as $log): ?>
                            <td>
                                <img 
                                    class="img-square"
                                    title="<?= $log['Player']['name']?>" 
                                    alt="<?= $log['Player']['name']?>" 
                                    style="max-width: 100%"
                                    src="<?= $this->Gravatar->get($log['Player']['email'], 100); ?>"/>
                                <br/>
                                <div class="xp">
                                    <?= h($log['Player']['name']) ?><br/>
                                    <strong><?= number_format($log? $log['XpLog']['xp'] : 0) ?> XP</strong>
                                </div>
                            </td>
                        <? endforeach; ?>        
                        <? for ($i = 0; $i < (count($players) - count($entries)); $i++): ?>
                            <td><div style="height: 75px"></div></td>
                        <?endfor;?>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
        <br/><br/>
        <div id="newtable"></div>
    </div>
</div>

<script type="text/javascript">
    function transpose(M) {  
       var T = [];  
       for(var i in M)  
          for (var j in M[i])  
             (T[j] = T[j] || [])[i] = M[i][j];  
       return T;  
    }  

   var aTd = [];  
   var i=0;  
  
   // loop through all rows :  
   $('#xp-table tr').each(function(){  
  
      aTd[i] = [];  
  
      j= 0;  
  
      // loop through all cell of each row ...  
      $('td', $(this)).each(function(){  
  
         // ... and save them :  
         aTd[i][j] = $(this).clone();  
  
         j++;  
      })  
  
      i++;  
   })  
  
   // transpose the array (see 'transpose' function above)  
   aTd_tr = transpose(aTd);  
  
   // create a new empty table  
   $('#xp-table').html('');
   var newTable = $('#xp-table'); 
  
   // populate it with cells from the transposed matrix :  
   $.each(aTd_tr, function(index1){  
      var tr = $('<tr>');
      newTable.append(tr);  
      $.each(aTd_tr[index1], function(index2, value2){  
         tr.append(value2);  
      })  
   });  
  
   $('#newtable').append(newTable);  
      
</script>