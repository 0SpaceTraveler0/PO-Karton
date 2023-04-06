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
        let efficiency = item['efficiency'] + "%";

        let td_width = document.createElement("td");
        let width = item['width'];

        let td_width_840 = document.createElement("td");
        let width_840 = item['width_material_840'];

        let td_width_1050 = document.createElement("td");
        let width_1050 = item['width_material_1050'];

        let td_width_1260 = document.createElement("td");
        let width_1260 = item['width_material_1260'];

        let td_width_1400 = document.createElement("td");
        let width_1400 = item['width_material_1400'];

        let td_material = document.createElement("td");
        let material = item['material']

        let td_quantity = document.createElement("td");
        let quantity = item['quantity']

        let td_remaining_quantity = document.createElement("td");
        let remaining_quantity = item['remaining_quantity']

        let td_img = document.createElement("td");
        console.log(item)
        let img = ' ';
        /*check_box.type="checkbox";
        check_box.className="work";*/
        if(item['urgent'] == '1'){
            img = '&#128293';
        }

        td_id.innerHTML = id;
        td_combined_passport.innerHTML = combined_passport;
        td_customer.innerHTML = customer;
        td_date.innerHTML = date;
        td_efficiency.innerHTML = efficiency;
        td_width.innerHTML = width;
        td_width_840.innerHTML = width_840;
        td_width_1050.innerHTML = width_1050;
        td_width_1260.innerHTML = width_1260;
        td_width_1400.innerHTML = width_1400;
        td_material.innerHTML = material;
        td_quantity.innerHTML = quantity;
        td_remaining_quantity.innerHTML = remaining_quantity;
        td_img.innerHTML = img;


        trElement.appendChild(td_id);
        trElement.appendChild(td_combined_passport);
        trElement.appendChild(td_customer);
        trElement.appendChild(td_date);
        trElement.appendChild(td_efficiency);
        trElement.appendChild(td_width);
        trElement.appendChild(td_width_840);
        trElement.appendChild(td_width_1050);
        trElement.appendChild(td_width_1260);
        trElement.appendChild(td_width_1400);
        trElement.appendChild(td_material);
        trElement.appendChild(td_quantity);
        trElement.appendChild(td_remaining_quantity);
        trElement.appendChild(td_img);

        let x = document.getElementById("tbd");

        x.appendChild(trElement);
    })
}