export async function switchNetwork(chainIdHex: string): Promise<boolean> {
  try {
    await rpcRequest("wallet_switchEthereumChain", [{ chainId: chainIdHex }])
    return true;
  } catch (error) {
    console.error(error);
    return false;
  }
}

export async function rpcRequest(method: string, params?: any[]): Promise<unknown> {
  const provider = (window as any).ethereum;
  if (!provider) {
    throw new Error("MetaMask not found!");
  }
  return await provider.request({
    method,
    params,
  });
}
