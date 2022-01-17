export default function () {
  return {
    orders: {
      data: [],
      ready: false,
      available: true,
      isLoadMore: false,
      canLoadMore: false
    },
    customer_order: {
      data: [],
      ready: false,
      available: true,
      isLoadMore: false,
      canLoadMore: false
    },
    invoice: null,
    transaction: null
  }
}
