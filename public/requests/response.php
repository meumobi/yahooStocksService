<?php
$response = '{"query":{"count":1,"created":"2014-05-12T19:42:12Z","lang":"en-US","results":{"quote":{"symbol":"SANB11.SA","Ask":"15.03","AverageDailyVolume":"0","Bid":"15.02","AskRealtime":"15.03","BidRealtime":"15.02","BookValue":"0.00","Change_PercentChange":"+0.03 - +0.20%","Change":"+0.03","Commission":null,"ChangeRealtime":"+0.03","AfterHoursChangeRealtime":"N/A - N/A","DividendShare":"0.00","LastTradeDate":"5/12/2014","TradeDate":null,"EarningsShare":"0.00","ErrorIndicationreturnedforsymbolchangedinvalid":null,"EPSEstimateCurrentYear":"0.00","EPSEstimateNextYear":"0.00","EPSEstimateNextQuarter":"0.00","DaysLow":"14.87","DaysHigh":"15.08","YearLow":"0.00","YearHigh":"0.00","HoldingsGainPercent":"- - -","AnnualizedGain":null,"HoldingsGain":null,"HoldingsGainPercentRealtime":"N/A - N/A","HoldingsGainRealtime":null,"MoreInfo":"ned","OrderBookRealtime":null,"MarketCapitalization":null,"MarketCapRealtime":null,"EBITDA":"0","ChangeFromYearLow":null,"PercentChangeFromYearLow":null,"LastTradeRealtimeWithTime":"N/A - 15.02","ChangePercentRealtime":"N/A - +0.20%","ChangeFromYearHigh":null,"PercebtChangeFromYearHigh":null,"LastTradeWithTime":"3:42pm - 15.02","LastTradePriceOnly":"15.02","HighLimit":null,"LowLimit":null,"DaysRange":"14.87 - 15.08","DaysRangeRealtime":"N/A - N/A","FiftydayMovingAverage":"0.00","TwoHundreddayMovingAverage":"0.00","ChangeFromTwoHundreddayMovingAverage":null,"PercentChangeFromTwoHundreddayMovingAverage":null,"ChangeFromFiftydayMovingAverage":null,"PercentChangeFromFiftydayMovingAverage":null,"Name":"SANTANDER BR-UNT","Notes":null,"Open":"15.05","PreviousClose":"14.99","PricePaid":null,"ChangeinPercent":"+0.20%","PriceSales":null,"PriceBook":null,"ExDividendDate":null,"PERatio":null,"DividendPayDate":null,"PERatioRealtime":null,"PEGRatio":null,"PriceEPSEstimateCurrentYear":null,"PriceEPSEstimateNextYear":null,"Symbol":"SANB11.SA","SharesOwned":null,"ShortRatio":null,"LastTradeTime":"3:42pm","TickerTrend":" +===== ","OneyrTargetPrice":null,"Volume":"1809600","HoldingsValue":null,"HoldingsValueRealtime":null,"YearRange":"0.00 - 0.00","DaysValueChange":"- - +0.20%","DaysValueChangeRealtime":"N/A - N/A","StockExchange":"Sao Paolo","DividendYield":null,"PercentChange":"+0.20%"}}}}';
$delay = isset($_GET['delay']) ? $_GET['delay'] : 0;
sleep($delay);
header('Content-Type: application/json');
echo $response;