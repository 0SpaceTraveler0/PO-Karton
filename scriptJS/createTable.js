function createTableBody(data) {
    
    data.forEach((item, index) => {
        let trElement = document.createElement("tr");
        trElement.id = Math.random();

        let td_id = document.createElement("td");
        td_id.className = 'id';
        let id = item['id']

        let td_combined_passport = document.createElement("td");
        let combined_passport = item['id_other_order']

        let td_customer = document.createElement("td");
        let customer = item['customer']

        let td_date = document.createElement("td");
        let date = item['shipping_date']

        let td_efficiency = document.createElement("td");
        let efficiency = item['efficiency'].toFixed(2) + "%";

        let td_width = document.createElement("td");
        let width = item['width'];

        let td_material = document.createElement("td");
        let material = item['material']

        let td_quantity = document.createElement("td");
        let quantity = item['quantity']

        let td_remaining_quantity = document.createElement("td");
        let remaining_quantity = item['remaining_quantity']
        
        let td_work = document.createElement("td");
        let check_box = document.createElement('input');;
        check_box.type="checkbox";
        check_box.className="work";
        if(item['flag'] == 59){
            check_box.checked=true;
        }

        td_id.innerHTML = id;
        td_combined_passport.innerHTML = combined_passport;
        td_customer.innerHTML = customer;
        td_date.innerHTML = date;
        td_efficiency.innerHTML = efficiency;
        td_width.innerHTML = width;
        td_material.innerHTML = material;
        td_quantity.innerHTML = quantity;
        td_remaining_quantity.innerHTML = remaining_quantity;
        td_work.innerHTML = check_box;

        trElement.appendChild(td_id);
        trElement.appendChild(td_combined_passport);
        trElement.appendChild(td_customer);
        trElement.appendChild(td_date);
        trElement.appendChild(td_efficiency);
        trElement.appendChild(td_width);
        trElement.appendChild(td_material);
        trElement.appendChild(td_quantity);
        trElement.appendChild(td_remaining_quantity);

        //td_work.appendChild(check_box);
        trElement.appendChild(check_box);

        let x = document.getElementById("tbd");

        x.appendChild(trElement);
    })
}