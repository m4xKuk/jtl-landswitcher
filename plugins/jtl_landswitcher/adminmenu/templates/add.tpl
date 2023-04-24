<form method="post" class="w-25">
  {$jtl_token}
  <!-- <input type="hidden" name="kPluginAdminMenu" value="{$menuID}"> -->
  <div class="form-group">
    <label for="exampleInputPassword1">Url</label>
    <input class="form-control" id="exampleInputPassword1" required type="url" name="redirect_url" value="" />
  </div>
  <div class="form-group">
    <label for="exampleFormControlSelect1">Example select</label>
    <select name="cities" class="form-control" id="exampleFormControlSelect1">
      {foreach from=$cities item=city}
      <option value="{$city->cISO}">{$city->name}</option>
      {/foreach}
    </select>
  </div>
  <button name="add" value="1" class="btn btn-success" type="submit">
    {__('Add new url')}
  </button>
</form>

<table class="table mt-2">
  <tr>
    <th>cISO</th>
    <th>City</th>
    <th>Url</th>
    <th colspan="2">Action</th>
  </tr>
  {foreach from=$objects item=foo}
  <tr>
    <td>{$foo.cISO}</td>
    <td>{$foo.name}</td>
    <td class="url-redirect">
      <span>{$foo.url|escape:'html'}</span>
      <form method="post" style="display: none;">
        {$jtl_token}
        <!-- <input type="hidden" name="kPluginAdminMenu" value="{$menuID}"> -->
        <input type="hidden" name="id" value="{$foo.id}">
        <div class="form-group">
          <input class="form-control" id="" required type="url" name="redirect_url" value="{$foo.url|escape:'html'}" />
        </div>
        <button type="submit" name="edit" value="edit" class="btn btn-primary">Save</button>
        <button type="button" onclick="cancel(this); return false;" class="btn btn-danger btn-cansel">Cancel</button>
      </form>
    </td>
    <td><a href="#"  onclick="edit(this, {$foo}); return false;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil"
        viewBox="0 0 16 16">
        <path
          d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
      </svg></a></td>
    <td><a href="#"  onclick="del(this, {$foo.id}); return false;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash"
        viewBox="0 0 16 16">
        <path
          d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
        <path
          d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
      </svg></a></td>
  </tr>
  {/foreach}
</table>

<script type="text/javascript">
  function del(el, id)
  {
    var row = $(el).parents("tr");
    $.ajax({
      type: "post",
      url: "{$adminURL}",
      data:  {
        id: id,
        action: "delete",
      },
      dataType: "html",
      success: function (response) {
        response = $.parseJSON(response)
        if(response.status == 'success'){
          row.remove()
          alert(response.message)
        }
      }
    });
  }

  function edit(el, item) {
    var row = $(el).parents("tr");
    var field = row.find('td.url-redirect span');
    var field_form = row.find('td.url-redirect form');
    field.hide();
    field_form.show();
  }

  function cancel(el) {
    var row = $(el).parents("tr");
    var field = row.find('td.url-redirect span');
    var field_form = row.find('td.url-redirect form');
    field.show();
    field_form.hide();
  }
</script>