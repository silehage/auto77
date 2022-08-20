
export function getFormOrder (state) {

  let order = {...state.formOrder}

  order.total = sumTotal(order)

  return order

}

function sumTotal(order) {
  if(order.coupon_discount) {
    return (order.subtotal-order.coupon_discount)+parseInt(order.shipping_cost) + parseInt(order.service_fee)
  }
  return order.subtotal + parseInt(order.shipping_cost) + parseInt(order.service_fee)

}
