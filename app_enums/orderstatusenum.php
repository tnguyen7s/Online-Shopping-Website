<?php
	class OrderStatusEnum
	{
		const IsProcessedByClient=0;
		const WaitingForSellerResponse=1;
		const TransactionProcessing = 2;
		const TransactionCompleted = 3;
		const IsCancelled = 4;
		const CancellationRequested = 5;
	}
?>