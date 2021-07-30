@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('')}}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{asset('')}}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<style>
.link{
  color:blue;
  cursor:pointer;
}
.link:hover{
  text-decoration: underline;
}
.hidden{
  display: none;
}
.dataTables_length{
  padding-left: 10px;
  padding-top: 15px;
}
.dataTables_filter{
  padding-right: 10px;
  padding-top: 15px;
}
.dataTables_info{
  padding-left: 10px;
  padding-bottom: 15px;
}
.dataTables_paginate{
  padding-right: 10px;
  padding-bottom: 15px;
}
</style>
@stop

@section('content')
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Form CRUD</h1>
          </div>
        </div>
      </div>
    </div>
    
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <form class="card" id="form">
              <div class="card-header">
                <h5 class="m-0" id="form-name">Form Create</h5>
              </div>
              <div class="card-body">
                {{csrf_field()}}
                <input type="hidden" name="id">
                <input type="hidden" name="_method" value="POST">
                <div class="row">
                  <div class="col-md-6">
                    <label>Nama</label>
                    <input type="text" name="nama" required class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Harga Beli</label>
                    <input type="number" name="harga_beli" required class="form-control">
                  </div>
                  <div class="col-md-12">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control"></textarea>
                  </div>
                </div>
              </div>
              <div class="card-footer" style="text-align:right;">
                <button type="button" onclick="hapusItem()" id="btn-hapus" class="btn btn-danger hidden">Hapus</button>
                <button type="button" onclick="clearForm()" class="btn btn-default">Clear Form</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>

            <div class="card">
              <div class="card-header">
                <h5 class="m-0">Master Item</h5>
              </div>
              <div class="card-body p-0">
                <table class="table table-bordered" id="table-list">
                  <thead>
                    <tr>
                      <th>Foto</th>
                      <th>Nama</th>
                      <th>Deskripsi</th>
                      <th>Harga Beli</th>
                      <th>Last Update</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@stop

@section('js')
<script src="{{asset('')}}plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{asset('')}}plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{asset('')}}plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('')}}plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<script>
let data = []

const table = $("#table-list").DataTable({
  // "scrollX":true,
  "pageLength": 5,
  "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
  "bLengthChange": true,
  "bFilter": true,
  "bInfo": true,
  "processing":true,
  "bServerSide": true,
  "order": [[ 1, "asc" ]],
  "ajax":{
    url:"{{url('data_gudang')}}",
    type:"POST",
    data:function(d){
      d._token = "{{csrf_token()}}"
    }
  },
  columns:[
    {
      "render": function(data, type, row, meta){
        return row.foto
      }
    },
    {
      "render": function(data, type, row, meta){
        return row.nama
      }
    },
    {
      "render": function(data, type, row, meta){
        return row.deskripsi
      }
    },
    {
      "render": function(data, type, row, meta){
        return row.harga_beli
      }
    },
    {
      "render": function(data, type, row, meta){
        return row.updated_at
      }
    }
  ]
});

function hapusItem(){
  const id = $("#form [name='id']").val()
  const c = confirm("Anda yakin akan menghapus data item tersebut ?")

  if(c===true){
    $.ajax({
      url:`{{url('delete_item')}}?id=${id}`,
      success:res=>{
        clearForm()
        // ambilDataMasterItem()
      },
      error:(err)=>{
        alert(err)
      }
    })
  }
}

function clearForm(){
  $("#form [name='id']").val('')
  $("#form [name='nama']").val('')
  $("#form [name='harga_beli']").val('')
  $("#form [name='deskripsi']").val('')
  
  $("#form-name").text("Form Create")
  $("#form [name='_method']").val('POST')
  $("#btn-hapus").addClass('hidden')
}

const ambilSatuData = (id)=>{
  $.ajax({
    url:`{{url('detail')}}?id=${id}`,
    success:function(res){
      $("#form [name='id']").val(id)
      $("#form [name='nama']").val(res.nama)
      $("#form [name='harga_beli']").val(res.harga_beli)
      $("#form [name='deskripsi']").val(res.deskripsi)
      $("#form-name").text("Form Update")
      $("#form [name='_method']").val('PATCH')

      $("#btn-hapus").removeClass('hidden')
    }
  })
}

function ambilDataMasterItem(){
  const url = "{{url('list_master_item')}}"
  $.ajax({
    url,
    success:function(list_master_item){
      console.log(list_master_item)
      let tampilan = '';
      $("#table-list tbody").children().remove()
      for(let i=0;i<list_master_item.length;i++){
        tampilan+=`
        <tr>
          <td>${list_master_item[i].foto||'-'}</td>
          <td><span class="link" role="link" onclick="ambilSatuData(${list_master_item[i].id})">${list_master_item[i].nama||'-'}</span></td>
          <td>${list_master_item[i].deskripsi||'-'}</td>
          <td>${list_master_item[i].harga_beli||'-'}</td>
          <td>${list_master_item[i].updated_at||'-'}</td>
        </tr>
        `
      }
      $("#table-list tbody").append(tampilan)
    },
    error:function(e){
      console.log(e)
      alert("Terjadi kesalahan ")
    }
  })
}

// ambilDataMasterItem()

$("#form").on('submit',function(event){
  event.preventDefault()
  submitForm()
})

function submitForm(){
  let form = $("#form");
  const url = "{{url('master_item')}}";
  $.ajax({
    url,
    method:"POST",
    data:form.serialize(),
    success:function(response){
      // ambilDataMasterItem()
    },
    error:function(err){
      console.log(err)
      alert("Ada Kesalahan")
    }
  })
}
</script>
@stop