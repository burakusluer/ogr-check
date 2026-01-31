declare var jQuery:any;
declare const ogrTakipMainData: { wp_nonce:string,users:any }
document.addEventListener("DOMContentLoaded",function () {
   renderOgrTable()
   function addOgrTableBodyRow(id,username) {
      const tableRow:HTMLTableElement= document.querySelector("table#ogr-takip-table");
      const newRow:HTMLTableRowElement=tableRow.insertRow();
      newRow.insertAdjacentHTML("beforeend",`<td>${id}</td><td>${username}</td><td><button class="button-primary button-danger">Burdayım</button></td>`)
   }

   function renderOgrTable(){
      jQuery("table#ogr-takip-table tbody tr").remove()
      for (const user of ogrTakipMainData.users) {
         addOgrTableBodyRow(user.ID,user.data.user_login)
      }
   }
});