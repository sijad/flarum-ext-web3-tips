import {
  BigNumber,
  parseFixed,
} from "@ethersproject/bignumber";

export function parseUnits(
  value: string,
  decimals: number,
): BigNumber {
  return parseFixed(value, decimals);
}
